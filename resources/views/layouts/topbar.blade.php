<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'QA Management')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>* { font-family: 'Inter', sans-serif; } body { background: #0c0f1a; } ::-webkit-scrollbar{width:5px;height:5px} ::-webkit-scrollbar-track{background:#0c0f1a} ::-webkit-scrollbar-thumb{background:rgba(99,102,241,.3);border-radius:99px}</style>
</head>
<body class="h-full font-sans text-slate-100 overflow-y-auto bg-[#0b0f19]" x-data="{}">

    <!-- TOPBAR -->
    <header class="h-16 border-b sticky top-0 z-40" style="background:rgba(12,15,26,.85);backdrop-filter:blur(12px);border-color:rgba(255,255,255,.06);">
        <div class="max-w-7xl mx-auto px-8 h-full flex items-center justify-between relative">
        <div class="flex items-center space-x-3 w-1/3">
            <img src="{{ asset('image/icon-aldo.png') }}" alt="Logo" class="w-9 h-9 rounded-xl object-cover">
            <span class="font-bold text-lg text-white tracking-wide">QA Platform</span>
        </div>

            <!-- DEVELOPER LINKS CENTERED -->
            <div class="hidden md:flex flex-1 justify-center space-x-2 w-1/3">
                @if(auth()->check() && auth()->user()->role === 'Developer')
                    <a href="{{ route('dashboard.developer') }}" class="px-3 py-1.5 text-xs font-semibold {{ request()->routeIs('dashboard.developer') ? 'text-white bg-white/[0.1]' : 'text-slate-300 hover:text-white bg-white/[0.03] hover:bg-white/[0.08]' }} rounded-lg transition border border-white/[0.05]">Dashboard</a>
                    <a href="{{ route('notifications.timeline') }}" class="px-3 py-1.5 text-xs font-semibold {{ request()->routeIs('notifications.timeline') ? 'text-white bg-white/[0.1]' : 'text-slate-300 hover:text-white bg-white/[0.03] hover:bg-white/[0.08]' }} rounded-lg transition border border-white/[0.05]">Notifikasi</a>
                    <a href="{{ route('bugs.index') }}" class="px-3 py-1.5 text-xs font-semibold {{ request()->routeIs('bugs.index') ? 'text-white bg-white/[0.1]' : 'text-slate-300 hover:text-white bg-white/[0.03] hover:bg-white/[0.08]' }} rounded-lg transition border border-white/[0.05]">Bug Report</a>
                @endif
            </div>

            <div class="flex items-center justify-end space-x-3 w-1/3">

                {{-- ════ BELL ICON NOTIFIKASI ════ --}}
                @php
                    $unreadCount = \App\Models\BugNotification::where('user_id', auth()->id())
                        ->where('is_read', false)->count();
                    $recentNotifs = \App\Models\BugNotification::where('user_id', auth()->id())
                        ->with('bug')->latest()->take(8)->get();
                @endphp
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button @click="open = !open"
                            type="button"
                            class="relative p-2 rounded-xl text-slate-400 hover:text-white hover:bg-white/5 transition cursor-pointer"
                            title="Notifikasi">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        @if($unreadCount > 0)
                        <span class="absolute -top-0.5 -right-0.5 w-4 h-4 rounded-full text-[9px] font-bold text-white flex items-center justify-center"
                              style="background:#4f46e5;">
                            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                        </span>
                        @endif
                    </button>

                    {{-- DROPDOWN PANEL --}}
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         x-cloak
                         style="display:none;"
                         class="absolute right-0 mt-2 w-80 rounded-2xl border shadow-2xl z-50 overflow-hidden"
                         style="background:#111827; border-color:rgba(255,255,255,0.07);">

                        {{-- Header --}}
                        <div class="px-4 py-3 border-b flex items-center justify-between" style="border-color:rgba(255,255,255,0.06);">
                            <span class="text-sm font-bold text-white">Notifikasi</span>
                            @if($unreadCount > 0)
                            <form action="{{ route('notifications.read-all') }}" method="POST">
                                @csrf
                                <button type="submit" class="text-[10px] text-indigo-400 hover:text-indigo-300 font-semibold cursor-pointer">
                                    Tandai semua dibaca
                                </button>
                            </form>
                            @endif
                        </div>

                        {{-- List --}}
                        <div class="max-h-72 overflow-y-auto">
                            @if($recentNotifs->isEmpty())
                            <div class="px-4 py-8 text-center text-slate-500 text-xs">Belum ada notifikasi</div>
                            @else
                            @foreach($recentNotifs as $notif)
                            <a href="{{ route('notifications.read', $notif->id) }}"
                               class="flex items-start gap-3 px-4 py-3 hover:bg-white/[0.03] transition border-b"
                               style="border-color:rgba(255,255,255,0.04);">
                                <span class="mt-1 w-2 h-2 rounded-full shrink-0 {{ $notif->is_read ? 'bg-slate-700' : 'bg-indigo-500' }}"></span>
                                <div class="min-w-0">
                                    <p class="text-xs {{ $notif->is_read ? 'text-slate-500' : 'text-slate-200' }} leading-snug">
                                        {{ $notif->message }}
                                    </p>
                                    <p class="text-[10px] text-slate-600 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                                </div>
                            </a>
                            @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                {{-- ════════════════════════════════ --}}

                {{-- Avatar + nama jadi tombol pembuka modal profile --}}
                <button
                    type="button"
                    @click="$dispatch('open-profile-modal')"
                    class="flex items-center space-x-3 cursor-pointer text-left"
                    title="Buka profil"
                >
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

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-slate-400 hover:text-red-400 p-2 rounded-xl hover:bg-red-500/10 transition cursor-pointer" title="Keluar / Logout">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </button>
                </form>
            </div>

        </div>
    </header>

    {{-- Hero section: beda-beda tiap halaman, jadi bisa dioverride lewat @section('hero') --}}
    @hasSection('hero')
        <div>
            <div class="max-w-7xl mx-auto px-8 py-8">
                @yield('hero')
            </div>
        </div>
    @endif

    <main class="p-8 space-y-6 max-w-7xl mx-auto">
        @yield('content')
    </main>

    <x-profile-modal />

</body>
</html>