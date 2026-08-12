<!DOCTYPE html>
<html lang="id" class="h-full bg-[#0b0f19]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'QA Platform')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-full font-sans text-slate-100 overflow-y-auto bg-[#0b0f19]" x-data="{}">

    <!-- TOPBAR -->
    <header class="h-20 border-b border-slate-800/80 sticky top-0 bg-[#0b0f19]/80 backdrop-blur-md z-40">
        <div class="max-w-7xl mx-auto px-8 h-full flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-teal-500 to-cyan-600 flex items-center justify-center font-bold text-white shadow-lg shadow-indigo-500/30">Q</div>
                <span class="font-bold text-lg text-white tracking-wide">QA Platform</span>
            </div>
            <div class="flex items-center space-x-4">
                {{-- Avatar + nama jadi tombol pembuka modal profile, sama seperti di sidebar --}}
                <button
                    type="button"
                    @click="$dispatch('open-profile-modal')"
                    class="flex items-center space-x-3 cursor-pointer text-left"
                    title="Buka profil"
                >
                    <div class="w-9 h-9 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center font-bold text-xs border border-indigo-500/30 overflow-hidden">
                        @if(auth()->user()->photo_path)
                            <img src="{{ Storage::url(auth()->user()->photo_path) }}" class="w-full h-full object-cover" alt="Foto profil">
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