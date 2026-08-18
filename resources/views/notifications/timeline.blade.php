<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi - QA Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        * { font-family: 'Inter', sans-serif; }
        body { background: #0c0f1a; }
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: #0c0f1a; }
        ::-webkit-scrollbar-thumb { background: rgba(99,102,241,0.3); border-radius: 99px; }
        @keyframes fadeIn { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }
        .fade-in { animation: fadeIn 0.4s ease both; }
    </style>
</head>
<body class="h-full text-slate-100 flex overflow-hidden" x-data="{ sidebarOpen: false }">

{{-- SIDEBAR (Admin & QA) --}}
@if(auth()->user()->role !== 'Developer')
    <x-sidebar />
@endif

{{-- MAIN WRAPPER --}}
<div class="{{ auth()->user()->role !== 'Developer' ? 'flex-1 flex flex-col min-w-0 overflow-y-auto h-full' : 'w-full flex flex-col min-h-screen' }}">

    {{-- TOPBAR --}}
    @if(auth()->user()->role !== 'Developer')
    <header class="h-16 border-b border-white/[0.06] px-4 sm:px-8 flex items-center justify-between sticky top-0 z-30"
            style="background:rgba(12,15,26,0.85); backdrop-filter:blur(12px);">
        <div class="flex items-center gap-3">
            <button @click="$dispatch('toggle-sidebar')" class="md:hidden p-2 rounded-xl text-slate-400 hover:text-white border border-white/[0.06]" style="background:#111827;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <div>
                <div class="text-[10px] text-indigo-400 font-bold tracking-widest uppercase">Overview</div>
                <div class="text-sm font-bold text-white">Notifikasi Saya</div>
            </div>
        </div>
        @if($unreadCount > 0)
        <form method="POST" action="{{ route('notifications.read-all') }}">
            @csrf
            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-semibold shadow-lg shadow-indigo-600/30 transition">
                ✓ Tandai Semua Dibaca
            </button>
        </form>
        @endif
    </header>
    @else
    <header class="h-16 border-b sticky top-0 z-40" style="background:rgba(12,15,26,.85);backdrop-filter:blur(12px);border-color:rgba(255,255,255,.06);">
        <div class="max-w-7xl mx-auto px-8 h-full flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-teal-500 to-cyan-600 flex items-center justify-center font-bold text-white">Q</div>
                <span class="font-bold text-lg text-white">QA Platform</span>
            </div>
            <div class="hidden md:flex space-x-2">
                <a href="{{ route('dashboard.developer') }}" class="px-3 py-1.5 text-xs font-semibold text-slate-300 hover:text-white bg-white/[0.03] hover:bg-white/[0.08] rounded-lg transition border border-white/[0.05]">Dashboard</a>
                <a href="{{ route('notifications.timeline') }}" class="px-3 py-1.5 text-xs font-semibold text-white bg-white/[0.1] rounded-lg transition border border-white/[0.1]">Notifikasi</a>
                <a href="{{ route('bugs.index') }}" class="px-3 py-1.5 text-xs font-semibold text-slate-300 hover:text-white bg-white/[0.03] hover:bg-white/[0.08] rounded-lg transition border border-white/[0.05]">Bug Report</a>
            </div>
            <div class="flex items-center gap-3">
                @if($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.read-all') }}">@csrf
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-semibold shadow-lg shadow-indigo-600/30 transition">✓ Tandai Semua Dibaca</button>
                </form>
                @endif
                <form action="{{ route('logout') }}" method="POST">@csrf
                    <button type="submit" class="text-slate-400 hover:text-red-400 p-2 rounded-xl hover:bg-red-500/10 transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </header>
    @endif

    <main class="p-4 sm:p-6 lg:p-8 space-y-6 fade-in max-w-5xl mx-auto w-full">

        <div class="mb-4">
            <h1 class="text-2xl font-bold text-white tracking-tight">Timeline Aktivitas</h1>
            <p class="text-sm text-slate-400 mt-1">Pantau semua pembaruan yang terkait dengan pengujian dan laporan bug.</p>
        </div>

        {{-- Filter Section --}}
        <div class="bg-[#111827] border border-white/[0.06] rounded-2xl p-5 shadow-lg">
            <form method="GET" class="flex flex-col sm:flex-row items-center gap-4">
                <div class="w-full sm:w-auto flex-1">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Tipe Notifikasi</label>
                    <select name="type" class="w-full px-4 py-2.5 bg-[#0c0f1a] border border-white/[0.1] rounded-xl text-xs text-white focus:outline-none focus:border-indigo-500">
                        <option value="">Semua Tipe</option>
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full sm:w-auto flex-1">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Status</label>
                    <select name="read_status" class="w-full px-4 py-2.5 bg-[#0c0f1a] border border-white/[0.1] rounded-xl text-xs text-white focus:outline-none focus:border-indigo-500">
                        <option value="">Semua Status</option>
                        <option value="unread" {{ request('read_status') == 'unread' ? 'selected' : '' }}>Belum Dibaca</option>
                        <option value="read" {{ request('read_status') == 'read' ? 'selected' : '' }}>Sudah Dibaca</option>
                    </select>
                </div>
                <div class="w-full sm:w-auto flex items-end pt-1">
                    <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-semibold shadow-lg shadow-indigo-600/30 transition">Terapkan Filter</button>
                </div>
                @if(request()->filled('type') || request()->filled('read_status'))
                <div class="w-full sm:w-auto flex items-end pt-1">
                    <a href="{{ route('notifications.timeline') }}" class="w-full sm:w-auto px-6 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-semibold transition text-center">Reset</a>
                </div>
                @endif
            </form>
        </div>

        {{-- Notifications Timeline --}}
        <div class="space-y-4">
            @forelse($notifications as $notif)
                <div class="bg-[#111827] border {{ !$notif->is_read ? 'border-indigo-500/50 shadow-lg shadow-indigo-500/10' : 'border-white/[0.06]' }} rounded-2xl p-5 sm:p-6 transition hover:border-indigo-500/30">
                    <div class="flex items-start justify-between">
                        <div class="flex-grow">
                            <div class="flex items-center gap-3 mb-1.5">
                                <h3 class="text-sm font-bold text-white">{{ $notif->message }}</h3>
                                @if(!$notif->is_read)
                                    <span class="px-2 py-0.5 bg-indigo-500 text-white text-[9px] font-bold tracking-widest uppercase rounded-lg">Baru</span>
                                @endif
                            </div>
                            <p class="text-xs font-mono text-slate-500 mb-4">{{ $notif->created_at->format('d M Y, H:i') }}</p>
                            @if($notif->bug)
                                <a href="{{ route('bugs.show', $notif->bug->id) }}" class="inline-flex items-center gap-2 px-3 py-1.5 bg-[#0c0f1a] border border-white/[0.1] rounded-xl text-xs text-indigo-400 hover:text-indigo-300 font-semibold transition hover:border-indigo-500/50">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    Lihat Detail Bug #{{ $notif->bug->id }}
                                </a>
                            @endif
                        </div>
                        @if(!$notif->is_read)
                            <form method="POST" action="{{ route('notifications.read', $notif->id) }}" class="ml-4 shrink-0">
                                @csrf @method('PATCH')
                                <button type="submit" class="px-3 py-1.5 bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 rounded-xl text-[10px] font-bold uppercase tracking-wider transition">
                                    Tandai Dibaca
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-[#111827] border border-white/[0.06] rounded-2xl p-12 text-center shadow-lg">
                    <p class="text-slate-400 text-sm">Belum ada notifikasi dengan filter yang dipilih.</p>
                </div>
            @endforelse

            @if($notifications->hasPages())
                <div class="mt-8">{{ $notifications->links() }}</div>
            @endif
        </div>
    </main>
</div>

<x-profile-modal />
</body>
</html>
