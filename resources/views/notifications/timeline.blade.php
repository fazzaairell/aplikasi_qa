<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi - QA Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>* { font-family: 'Inter', sans-serif; } body { background: #0c0f1a; } ::-webkit-scrollbar{width:5px;height:5px} ::-webkit-scrollbar-track{background:#0c0f1a} ::-webkit-scrollbar-thumb{background:rgba(99,102,241,.3);border-radius:99px}</style>

</head>
<body class="h-full font-sans text-slate-100 flex overflow-hidden" x-data="{ sidebarOpen: false }">

@if(auth()->user()->role !== 'Developer')
    <x-sidebar />
@endif

{{-- MAIN WRAPPER --}}
<div class="{{ auth()->user()->role !== 'Developer' ? 'flex-1 flex flex-col min-w-0 overflow-y-auto h-full' : 'w-full flex flex-col min-h-screen' }}">

    {{-- TOPBAR --}}
    @if(auth()->user()->role !== 'Developer')
    <header class="h-16 border-b border-white/[0.06] px-4 sm:px-8 flex items-center justify-between sticky top-0 z-30"
            style="background: rgba(12,15,26,0.85); backdrop-filter: blur(12px);">
        <div class="flex items-center gap-3">
            <button @click="$dispatch('toggle-sidebar')"
                    class="md:hidden p-2 rounded-xl text-slate-400 hover:text-white border border-white/[0.06] cursor-pointer"
                    style="background:#111827;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <h1 class="text-sm font-bold text-white">Notifikasi</h1>
        </div>
    </header>
    @else
    {{-- Developer: topbar penuh --}}
    <header class="h-16 border-b sticky top-0 z-40" style="background:rgba(12,15,26,.85);backdrop-filter:blur(12px);border-color:rgba(255,255,255,.06);">
        <div class="max-w-7xl mx-auto px-8 h-full flex items-center justify-between relative">
            <div class="flex items-center space-x-3 w-1/3">
                <img src="{{ asset('image/icon-aldo.png') }}" alt="Logo" class="w-9 h-9 rounded-xl object-cover">
                <span class="font-bold text-lg text-white tracking-wide">QA Platform</span>
            </div>
            <div class="hidden md:flex flex-1 justify-center space-x-2 w-1/3">
                <a href="{{ route('dashboard.developer') }}" class="px-3 py-1.5 text-xs font-semibold {{ request()->routeIs('dashboard.developer') ? 'text-white bg-white/[0.1]' : 'text-slate-300 hover:text-white bg-white/[0.03] hover:bg-white/[0.08]' }} rounded-lg transition border border-white/[0.05]">Dashboard</a>
                <a href="{{ route('notifications.timeline') }}" class="px-3 py-1.5 text-xs font-semibold text-white bg-white/[0.1] rounded-lg transition border border-white/[0.1]">Notifikasi</a>
                <a href="{{ route('bugs.index') }}" class="px-3 py-1.5 text-xs font-semibold {{ request()->routeIs('bugs.index') ? 'text-white bg-white/[0.1]' : 'text-slate-300 hover:text-white bg-white/[0.03] hover:bg-white/[0.08]' }} rounded-lg transition border border-white/[0.05]">Bug Report</a>
            </div>
            <div class="flex items-center justify-end space-x-3 w-1/3">
                @php
                    $unreadCount = \App\Models\BugNotification::where('user_id', auth()->id())->where('is_read', false)->count();
                    $recentNotifs = \App\Models\BugNotification::where('user_id', auth()->id())->with('bug')->latest()->take(8)->get();
                @endphp
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button @click="open = !open" type="button" class="relative p-2 rounded-xl text-slate-400 hover:text-white hover:bg-white/5 transition cursor-pointer" title="Notifikasi">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        @if($unreadCount > 0)
                        <span class="absolute -top-0.5 -right-0.5 w-4 h-4 rounded-full text-[9px] font-bold text-white flex items-center justify-center" style="background:#4f46e5;">
                            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                        </span>
                        @endif
                    </button>
                    <div x-show="open" x-cloak style="display:none;" class="absolute right-0 mt-2 w-80 rounded-2xl border shadow-2xl z-50 overflow-hidden" style="background:#111827; border-color:rgba(255,255,255,0.07);">
                        <div class="px-4 py-3 border-b flex items-center justify-between" style="border-color:rgba(255,255,255,0.06);">
                            <span class="text-sm font-bold text-white">Notifikasi</span>
                            @if($unreadCount > 0)
                            <form action="{{ route('notifications.read-all') }}" method="POST">@csrf
                                <button type="submit" class="text-[10px] text-indigo-400 hover:text-indigo-300 font-semibold cursor-pointer">Tandai semua dibaca</button>
                            </form>
                            @endif
                        </div>
                        <div class="max-h-72 overflow-y-auto">
                            @if($recentNotifs->isEmpty())
                            <div class="px-4 py-8 text-center text-slate-500 text-xs">Belum ada notifikasi</div>
                            @else
                            @foreach($recentNotifs as $notif)
                            <a href="{{ route('notifications.read', $notif->id) }}" class="flex items-start gap-3 px-4 py-3 hover:bg-white/[0.03] transition border-b" style="border-color:rgba(255,255,255,0.04);">
                                <span class="mt-1 w-2 h-2 rounded-full shrink-0 {{ $notif->is_read ? 'bg-slate-700' : 'bg-indigo-500' }}"></span>
                                <div class="min-w-0">
                                    <p class="text-xs {{ $notif->is_read ? 'text-slate-500' : 'text-slate-200' }} leading-snug">{{ $notif->message }}</p>
                                    <p class="text-[10px] text-slate-600 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                                </div>
                            </a>
                            @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                <button type="button" @click="$dispatch('open-profile-modal')" class="flex items-center space-x-3 cursor-pointer text-left" title="Buka profil">
                    <div class="w-9 h-9 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center font-bold text-xs border border-indigo-500/30 overflow-hidden">
                        @if(auth()->user()->photo_path)
                            <img src="{{ asset('uploads/' . auth()->user()->photo_path) }}" class="w-full h-full object-cover" alt="Foto profil">
                        @else
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        @endif
                    </div>
                    <div class="hidden sm:block">
                        <div class="text-xs font-bold text-white">{{ auth()->user()->name }}</div>
                        <div class="text-[10px] text-indigo-400 font-semibold">{{ auth()->user()->role }}</div>
                    </div>
                </button>
                <form action="{{ route('logout') }}" method="POST">@csrf
                    <button type="submit" class="text-slate-400 hover:text-red-400 p-2 rounded-xl hover:bg-red-500/10 transition cursor-pointer" title="Keluar">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </header>
    @endif

    <main class="p-4 sm:p-6 lg:p-8 space-y-6 {{ auth()->user()->role === 'Developer' ? 'max-w-7xl mx-auto w-full' : '' }}">

        <div class="mb-4">
            <div class="text-[10px] text-indigo-400 font-bold tracking-widest uppercase mb-1">NOTIFIKASI</div>
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