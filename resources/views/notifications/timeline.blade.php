<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi - TESTIFY</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>* { font-family: 'Inter', sans-serif; } body { background: #0c0f1a; } ::-webkit-scrollbar{width:5px;height:5px} ::-webkit-scrollbar-track{background:#0c0f1a} ::-webkit-scrollbar-thumb{background:rgba(99,102,241,.3);border-radius:99px}</style>

</head>
<body class="h-full font-sans text-slate-100 flex overflow-hidden" x-data="{ sidebarOpen: false }">

    <x-sidebar />

{{-- MAIN WRAPPER --}}
<div class="flex-1 flex flex-col min-w-0 overflow-y-auto h-full">

    {{-- TOPBAR --}}
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
        </div>
    </header>

    <main class="p-4 sm:p-6 lg:p-8 space-y-6">

        <div class="mb-2 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="text-[10px] text-indigo-400 font-bold tracking-widest uppercase mb-1">NOTIFIKASI</div>
                <h1 class="text-3xl font-bold text-white tracking-tight">Timeline Aktivitas</h1>
            </div>
            @if($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 border border-white/[0.1] hover:border-indigo-500/40 text-slate-300 hover:text-white rounded-full text-xs font-semibold transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Tandai semua dibaca
                    </button>
                </form>
            @endif
        </div>

        {{-- Filter Section --}}
        <div class="flex flex-wrap items-center gap-2">
            @php
                $readOptions = ['' => 'Semua', 'unread' => 'Belum dibaca', 'read' => 'Sudah dibaca'];
            @endphp
            @foreach($readOptions as $value => $label)
                <a href="{{ route('notifications.timeline', array_filter(['read_status' => $value, 'type' => request('type')])) }}"
                   class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition {{ request('read_status', '') == $value ? 'bg-indigo-600 text-white' : 'border border-white/[0.1] text-slate-400 hover:text-white hover:border-white/20' }}">
                    {{ $label }}
                </a>
            @endforeach

            <select onchange="if(this.value) window.location.href=this.value;"
                    class="ml-auto px-3.5 py-1.5 bg-[#111827] border border-white/[0.1] rounded-full text-xs text-slate-300 focus:outline-none focus:border-indigo-500/50">
                <option value="{{ route('notifications.timeline', array_filter(['read_status' => request('read_status')])) }}" {{ !request('type') ? 'selected' : '' }}>Semua tipe</option>
                @foreach($types as $key => $label)
                    <option value="{{ route('notifications.timeline', array_filter(['read_status' => request('read_status'), 'type' => $key])) }}" {{ request('type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        {{-- Notifications Timeline --}}
        <div class="rounded-2xl border border-white/[0.06] overflow-hidden" style="background:#111827;">
            @forelse($notifications as $notif)
                @php
                    $causer = $notif->causer;
                    $initials = $causer
                        ? collect(explode(' ', trim($causer->name)))->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode('')
                        : '?';
                    $avatarPalette = [
                        ['bg' => 'rgba(99,102,241,0.18)',  'text' => '#a5b4fc'],
                        ['bg' => 'rgba(212,90,90,0.18)',   'text' => '#f0a5a5'],
                        ['bg' => 'rgba(99,182,120,0.18)',  'text' => '#a3d9b1'],
                        ['bg' => 'rgba(217,151,40,0.18)',  'text' => '#f3cd85'],
                        ['bg' => 'rgba(93,170,202,0.18)',  'text' => '#9fd6ef'],
                    ];
                    $avatarColor = $avatarPalette[($causer->id ?? $notif->id) % count($avatarPalette)];
                    $assignee = $notif->bug?->assignee;
                    $assigneeInitials = $assignee
                        ? collect(explode(' ', trim($assignee->name)))->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode('')
                        : null;
                @endphp
                <div class="flex gap-3.5 px-4 sm:px-5 py-4 {{ !$loop->first ? 'border-t border-white/[0.06]' : '' }}">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-[11px] font-bold flex-shrink-0"
                         style="background:{{ $avatarColor['bg'] }}; color:{{ $avatarColor['text'] }};">
                        {{ strtoupper($initials) }}
                    </div>

                    <div class="flex-1 min-w-0">
                        <p class="text-[13.5px] leading-snug {{ !$notif->is_read ? 'font-bold text-white' : 'font-normal text-slate-300' }}">
                            {{ $notif->message }}
                            @if(!$notif->is_read)
                                <span class="inline-block w-1.5 h-1.5 rounded-full bg-indigo-500 ml-1 align-middle"></span>
                            @endif
                        </p>
                        <p class="text-[11.5px] text-slate-500 mt-1 mb-2.5">{{ $notif->created_at->format('d M, H:i') }}</p>

                        @if($notif->bug)
                            <div class="flex items-center gap-2 flex-wrap">
                                <a href="{{ route('bugs.show', $notif->bug->id) }}"
                                   class="inline-flex items-center gap-1.5 border border-white/[0.1] hover:border-indigo-500/40 text-slate-300 hover:text-indigo-300 text-[11px] px-2.5 py-1 rounded-full transition">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    Bug #{{ $notif->bug->id }}
                                </a>
                                @if($assignee)
                                    <span class="inline-flex items-center gap-1.5 border border-white/[0.1] text-slate-400 text-[11px] pl-1 pr-2.5 py-0.5 rounded-full">
                                        <span class="w-4 h-4 rounded-full bg-white/10 text-slate-200 text-[8px] font-bold flex items-center justify-center flex-shrink-0">{{ strtoupper($assigneeInitials) }}</span>
                                        Ditugaskan ke {{ $assignee->name }}
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>

                    @if(!$notif->is_read)
                        <form method="POST" action="{{ route('notifications.read', $notif->id) }}" class="flex-shrink-0 self-start">
                            @csrf @method('PATCH')
                            <button type="submit" title="Tandai dibaca" class="text-slate-500 hover:text-indigo-400 transition p-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </button>
                        </form>
                    @endif
                </div>
            @empty
                <div class="p-12 text-center">
                    <p class="text-slate-400 text-sm">Belum ada notifikasi dengan filter yang dipilih.</p>
                </div>
            @endforelse
        </div>

        @if($notifications->hasPages())
            <div class="mt-2">{{ $notifications->links() }}</div>
        @endif

    </main>
</div>

<x-profile-modal />
</body>
</html>