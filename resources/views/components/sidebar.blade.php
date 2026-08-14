<!-- SIDEBAR -->
<!-- Backdrop for mobile -->
<div x-data="{ sidebarOpen: false }"
     x-show="sidebarOpen"
     @toggle-sidebar.window="sidebarOpen = !sidebarOpen"
     @click="sidebarOpen = false"
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-black/80 z-40 md:hidden" style="display: none;"></div>

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
    @toggle-sidebar.window="sidebarOpen = !sidebarOpen"
    :class="[
        sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0',
        collapsed ? 'md:w-[72px]' : 'md:w-64',
        'w-64 flex fixed inset-y-0 left-0 z-50 md:relative'
    ]"
    class="flex-col justify-between shrink-0 h-full transition-all duration-300 ease-in-out"
    style="background: #0c0f1a; border-right: 1px solid rgba(255,255,255,0.06);"
>
    <div class="flex flex-col h-full">
        <!-- LOGO -->
        <div class="p-5 flex items-center justify-between" :class="collapsed ? 'md:justify-center md:px-3' : ''">
            <!-- Logo + Nama -->
            <div class="flex items-center space-x-3" x-show="!collapsed" x-transition>
                <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-white shrink-0 text-sm"
                     style="background: linear-gradient(135deg,#4f46e5,#7c3aed); box-shadow: 0 6px 16px rgba(79,70,229,0.3);">QA</div>
                <span class="font-bold text-base text-white tracking-wide whitespace-nowrap">QA Platform</span>
            </div>
            <!-- Logo saja (collapsed) -->
            <div class="hidden" x-show="collapsed" x-transition>
                <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-white text-sm"
                     style="background: linear-gradient(135deg,#4f46e5,#7c3aed);">QA</div>
            </div>

            <!-- Toggle Desktop -->
            <button @click="collapsed = !collapsed"
                    class="hidden md:flex items-center justify-center w-7 h-7 rounded-lg text-slate-500 hover:text-white transition cursor-pointer"
                    style="background:rgba(255,255,255,0.04);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <!-- Close Mobile -->
            <button @click="$dispatch('toggle-sidebar')" class="md:hidden text-slate-400 hover:text-white cursor-pointer">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- NAV -->
        <nav class="flex-1 px-3 space-y-0.5 mt-1 overflow-y-auto">
            @php
                $navItems = [
                    ['route' => 'dashboard',        'match' => 'dashboard',        'icon' => 'M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zM14 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z', 'label' => 'Dashboard'],
                    ['route' => 'projects.index',   'match' => 'projects.*',        'icon' => 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z', 'label' => 'Proyek'],
                    ['route' => 'requirements.index','match' => 'requirements.*',   'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', 'label' => 'Requirements'],
                    ['route' => 'test-suites.index','match' => 'test-suites.*',     'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10', 'label' => 'Test Suites'],
                    ['route' => 'test-runs.index',  'match' => 'test-runs.*',       'icon' => 'M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'label' => 'Test Runs'],
                    ['route' => 'bugs.index',       'match' => 'bugs.*',            'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z', 'label' => 'Bug Tracker'],
                    ['route' => 'users.index',      'match' => 'users.*',           'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', 'label' => 'Manajemen Pengguna'],
                ];
            @endphp

            @foreach($navItems as $item)
            @php
                $isActive = request()->routeIs($item['match']);
                $linkClasses = $isActive
                    ? 'bg-indigo-500/10 text-indigo-300 border border-indigo-500/20'
                    : 'text-slate-500 border border-transparent hover:bg-white/5 hover:text-slate-200';
            @endphp
            <a href="{{ route($item['route']) }}"
            :class="collapsed ? 'md:justify-center md:px-3' : ''"
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 group {{ $linkClasses }}"
            :title="collapsed ? '{{ $item['label'] }}' : ''">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $item['icon'] }}"/>
                </svg>
                <span x-show="!collapsed" x-transition class="whitespace-nowrap">{{ $item['label'] }}</span>
            </a>
            @endforeach
        </nav>

        <!-- USER PROFILE -->
        <div class="p-3 m-3 rounded-2xl" style="background:#111827; border:1px solid rgba(255,255,255,0.06);">
            <div class="flex items-center justify-between" :class="collapsed ? 'md:justify-center' : ''">
                <button type="button"
                        @click="$dispatch('open-profile-modal')"
                        class="flex items-center gap-2.5 cursor-pointer text-left min-w-0"
                        :class="collapsed ? 'md:justify-center' : ''"
                        title="Buka profil">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center font-bold text-xs border shrink-0 overflow-hidden"
                         style="background:rgba(99,102,241,0.15); color:#a5b4fc; border-color:rgba(99,102,241,0.3);">
                        @if(auth()->user()->photo_path)
                            <img src="{{ Storage::url(auth()->user()->photo_path) }}" class="w-full h-full object-cover" alt="Foto profil">
                        @else
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        @endif
                    </div>
                    <div x-show="!collapsed" x-transition class="min-w-0">
                        <div class="text-xs font-bold text-white truncate">{{ auth()->user()->name }}</div>
                        <div class="text-[10px] text-slate-500">{{ ucfirst(auth()->user()->role ?? 'User') }}</div>
                    </div>
                </button>

                <form action="{{ route('logout') }}" method="POST" x-show="!collapsed" x-transition class="inline shrink-0">
                    @csrf
                    <button type="submit"
                            class="p-1.5 rounded-lg text-slate-500 hover:text-red-400 hover:bg-red-500/10 transition cursor-pointer"
                            title="Keluar">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>