<!-- SIDEBAR -->
<aside
    x-data="{
        sidebarOpen: false,
        collapsed: localStorage.getItem('sidebarCollapsed') === 'true' ? true : false,
        init() {
            this.$watch('collapsed', (val) => {
                localStorage.setItem('sidebarCollapsed', val ? 'true' : 'false');
            });
        }
    }"
    :class="[
        sidebarOpen ? 'flex fixed inset-y-0 left-0 z-50' : 'hidden md:flex',
        collapsed ? 'md:w-20' : 'md:w-64',
        'w-64'
    ]"
    class="bg-[#0b0f19] border-r border-slate-800/80 flex-col justify-between shrink-0 h-full transition-all duration-300 ease-in-out"
>
    <div>
        <div class="p-6 flex items-center justify-between" :class="collapsed ? 'md:justify-center md:px-3' : ''">
            <!-- Logo + Nama (disembunyikan saat collapsed) -->
            <div class="flex items-center space-x-3" x-show="!collapsed" x-transition>
                <div class="w-9 h-9 rounded-xl bg-indigo-600 flex items-center justify-center font-bold text-white shadow-lg shadow-indigo-500/30 shrink-0">Q</div>
                <span class="font-bold text-lg text-white tracking-wide whitespace-nowrap">QA Platform</span>
            </div>

            <!-- Tombol Hamburger (Desktop) -->
            <button @click="collapsed = !collapsed" class="hidden md:block text-slate-400 hover:text-white cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>

            <!-- Tombol Close (Mobile) -->
            <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-white cursor-pointer">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <nav class="px-4 space-y-1.5 mt-2">
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" 
               :class="collapsed ? 'md:justify-center' : ''"
               class="flex items-center space-x-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-200 hover:bg-[#131b2e] transition text-sm font-medium"
               :class="@if(request()->routeIs('dashboard')) 'bg-indigo-600/10 text-indigo-400 border border-indigo-500/20' @endif"
               :title="collapsed ? 'Dashboard' : ''">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zM14 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"></path></svg>
                <span x-show="!collapsed" x-transition class="whitespace-nowrap">Dashboard</span>
            </a>

            <a href="{{ route('projects.index') }}" 
               :class="collapsed ? 'md:justify-center' : ''"
               class="flex items-center space-x-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-200 hover:bg-[#131b2e] transition text-sm font-medium"
               :class="@if(request()->routeIs('projects.*')) 'bg-indigo-600/10 text-indigo-400 border border-indigo-500/20' @endif"
               :title="collapsed ? 'Proyek' : ''">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                <span x-show="!collapsed" x-transition class="whitespace-nowrap">Proyek</span>
            </a>

            <a href="{{ route('requirements.index') }}" 
               :class="collapsed ? 'md:justify-center' : ''"
               class="flex items-center space-x-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-200 hover:bg-[#131b2e] transition text-sm font-medium"
               :class="@if(request()->routeIs('requirements.*')) 'bg-indigo-600/10 text-indigo-400 border border-indigo-500/20' @endif"
               :title="collapsed ? 'Requirements' : ''">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                <span x-show="!collapsed" x-transition class="whitespace-nowrap">Requirements</span>
            </a>

            <a href="{{ route('test-suites.index') }}" 
               :class="collapsed ? 'md:justify-center' : ''"
               class="flex items-center space-x-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-200 hover:bg-[#131b2e] transition text-sm font-medium"
               :class="@if(request()->routeIs('test-suites.*')) 'bg-indigo-600/10 text-indigo-400 border border-indigo-500/20' @endif"
               :title="collapsed ? 'Test Suites' : ''">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                <span x-show="!collapsed" x-transition class="whitespace-nowrap">Test Suites</span>
            </a>

            <a href="{{ route('test-runs.index') }}" 
               :class="collapsed ? 'md:justify-center' : ''"
               class="flex items-center space-x-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-200 hover:bg-[#131b2e] transition text-sm font-medium"
               :class="@if(request()->routeIs('test-runs.*')) 'bg-indigo-600/10 text-indigo-400 border border-indigo-500/20' @endif"
               :title="collapsed ? 'Test Runs' : ''">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span x-show="!collapsed" x-transition class="whitespace-nowrap">Test Runs</span>
            </a>

            <a href="{{ route('bugs.index') }}" 
               :class="collapsed ? 'md:justify-center' : ''"
               class="flex items-center space-x-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-200 hover:bg-[#131b2e] transition text-sm font-medium"
               :class="@if(request()->routeIs('bugs.*')) 'bg-indigo-600/10 text-indigo-400 border border-indigo-500/20' @endif"
               :title="collapsed ? 'Bug Tracker' : ''">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <span x-show="!collapsed" x-transition class="whitespace-nowrap">Bug Tracker</span>
            </a>

            <a href="{{ route('users.index') }}" 
               :class="collapsed ? 'md:justify-center' : ''"
               class="flex items-center space-x-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-200 hover:bg-[#131b2e] transition text-sm font-medium"
               :class="@if(request()->routeIs('users.*')) 'bg-indigo-600/10 text-indigo-400 border border-indigo-500/20' @endif"
               :title="collapsed ? 'Manajemen Pengguna' : ''">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <span x-show="!collapsed" x-transition class="whitespace-nowrap">Manajemen Pengguna</span>
            </a>
        </nav>
    </div>
<div class="p-4 m-4 rounded-2xl bg-[#131b2e] border border-slate-800/80 flex items-center justify-between" :class="collapsed ? 'md:justify-center' : ''">
 
    <button
        type="button"
        @click="$dispatch('open-profile-modal')"
        class="flex items-center space-x-3 cursor-pointer text-left"
        :class="collapsed ? 'md:justify-center' : ''"
        title="Buka profil"
    >
        <div class="w-9 h-9 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center font-bold text-xs border border-indigo-500/30 shrink-0 overflow-hidden">
            @if(auth()->user()->photo_path)
                <img src="{{ Storage::url(auth()->user()->photo_path) }}" class="w-full h-full object-cover" alt="Foto profil">
            @else
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            @endif
        </div>
        <div x-show="!collapsed" x-transition>
            <div class="text-xs font-bold text-white whitespace-nowrap">{{ auth()->user()->name }}</div>
            <div class="text-[10px] text-slate-400">{{ strtoupper(substr(auth()->user()->role ?? 'User', 0, 1)) }}</div>
        </div>
    </button>
 
    {{-- Tombol Logout (tetap sama seperti sebelumnya) --}}
    <form action="{{ route('logout') }}" method="POST" x-show="!collapsed" x-transition class="inline">
        @csrf
        <button type="submit" class="text-slate-400 hover:text-red-400 p-2 rounded-xl hover:bg-red-500/10 transition cursor-pointer" title="Keluar / Logout">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
            </svg>
        </button>
    </form>
</div>
 
{{-- Taruh ini SEKALI di layout utama (mis. layouts/app.blade.php), sebelum </body> --}}
{{-- <x-profile-modal /> --}}
</aside>