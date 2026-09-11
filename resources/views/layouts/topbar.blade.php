<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TESTIFY')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>* { font-family: 'Inter', sans-serif; } body { background: #0c0f1a; } ::-webkit-scrollbar{width:5px;height:5px} ::-webkit-scrollbar-track{background:#0c0f1a} ::-webkit-scrollbar-thumb{background:rgba(99,102,241,.3);border-radius:99px}</style>
    <link rel="icon" type="image/x-icon" href="{{ asset('image/icon-aldo.png') }}">

</head>
<body class="h-full font-sans text-slate-100 overflow-y-auto bg-[#0b0f19]" x-data="{}">

    {{-- Topbar sederhana untuk Admin / QA Tester --}}
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

            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input type="text" placeholder="Cari bug..." id="bug-search"
                       class="w-48 sm:w-64 md:w-80 pl-9 pr-3 py-2 rounded-xl text-xs text-white placeholder-slate-500 border border-white/[0.06] focus:outline-none focus:border-indigo-500/50 transition"
                       style="background:#111827;" oninput="filterBugs()">
            </div>
        </div>

        <div class="flex items-center space-x-3">
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

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-slate-400 hover:text-red-400 p-2 rounded-xl hover:bg-red-500/10 transition cursor-pointer" title="Keluar / Logout">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                </button>
            </form>
        </div>
    </header>

    {{-- Hero section: beda-beda tiap halaman, jadi bisa dioverride lewat @section('hero') --}}
    @hasSection('hero')
        <div>
            <div class="px-6 sm:px-8 py-8">
                @yield('hero')
            </div>
        </div>
    @endif

    <main class="px-6 sm:px-8 pb-8 space-y-6">
        @yield('content')
    </main>

    <x-profile-modal />

</body>
</html>
