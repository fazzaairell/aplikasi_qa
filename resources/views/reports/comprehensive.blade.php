<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprehensive Reports - QA Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        * { font-family: 'Inter', sans-serif; box-sizing: border-box; }
        body { background: #0c0f1a; }
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: #0c0f1a; }
        ::-webkit-scrollbar-thumb { background: rgba(99,102,241,0.3); border-radius: 99px; }
        @keyframes fadeIn { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }
        .fade-in { animation: fadeIn 0.4s ease both; }
        .metric-card { transition: transform 0.2s ease; }
        .metric-card:hover { transform: translateY(-2px); }
        .bar-fill { transition: width 0.6s cubic-bezier(.4,0,.2,1); }
    </style>

</head>
<body class="h-full text-slate-100 flex overflow-hidden" x-data="{ sidebarOpen: false }">

<x-sidebar />

<!-- MAIN CONTENT -->
<div class="flex-1 flex flex-col min-w-0 overflow-y-auto h-full">

    <!-- Topbar -->
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
            <div class="text-xs text-slate-500 font-medium">Laporan &rsaquo; <span class="text-slate-300">Comprehensive</span></div>
        </div>
        <div class="flex items-center gap-3">
            <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold" style="background:rgba(99,102,241,0.1); color:#818cf8; border:1px solid rgba(99,102,241,0.2);">LIVE</span>
        </div>
    </header>

    <div class="p-8 space-y-6 fade-in max-w-7xl mx-auto w-full">

        <!-- PAGE HEADER -->
        <div class="mb-2">
            <div class="flex items-center space-x-2 mb-1">
                <span class="w-2 h-2 rounded-full bg-indigo-400 animate-pulse"></span>
                <span class="text-[11px] text-indigo-400 font-bold tracking-widest uppercase">Comprehensive</span>
            </div>
            <h1 class="text-3xl font-bold text-white tracking-tight">Comprehensive Reports</h1>
            <p class="text-sm text-slate-400 mt-1">Analisis mendalam tentang status testing, bug, dan kualitas proyek.</p>
        </div>

        <!-- FILTER -->
        <div class="bg-[#111827] border border-slate-800/80 rounded-2xl p-5">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Filter Proyek</label>
                    <select name="project_id" onchange="this.form.submit()"
                            class="w-full px-3 py-2.5 bg-[#0c0f1a] border border-indigo-500/20 rounded-xl text-xs text-white focus:outline-none focus:border-indigo-500 transition">
                        <option value="">Semua Proyek</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}" {{ request('project_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    @if(request('project_id'))
                        <a href="{{ route('report.comprehensive') }}"
                           class="px-4 py-2.5 text-xs text-indigo-400 hover:text-indigo-300 font-semibold border border-indigo-500/20 rounded-xl transition"
                           style="background:rgba(99,102,241,0.05);">
                            Reset Filter
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- KEY METRICS -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <!-- Pass Rate -->
            <div class="metric-card rounded-2xl p-5 space-y-3"
                 style="background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.25);">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-wider">Pass Rate</span>
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center" style="background:rgba(16,185,129,0.15);">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="text-4xl font-extrabold text-white">{{ $passRate }}<span class="text-lg text-emerald-400">%</span></div>
                <div class="text-[11px] text-slate-400">{{ $resultsByStatus['Passed'] ?? 0 }} / {{ $totalResults }} passed</div>
            </div>

            <!-- Total Bugs -->
            <div class="metric-card rounded-2xl p-5 space-y-3"
                 style="background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.25);">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold text-rose-400 uppercase tracking-wider">Total Bugs</span>
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center" style="background:rgba(239,68,68,0.15);">
                        <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                </div>
                <div class="text-4xl font-extrabold text-white">{{ $totalBugs }}</div>
                <div class="text-[11px] text-slate-400">{{ $bugsByStatus['Open'] ?? 0 }} open, {{ $bugsByStatus['In Progress'] ?? 0 }} in progress</div>
            </div>

            <!-- Test Runs -->
            <div class="metric-card rounded-2xl p-5 space-y-3"
                 style="background:rgba(99,102,241,0.1); border:1px solid rgba(99,102,241,0.25);">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold text-indigo-400 uppercase tracking-wider">Test Runs</span>
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center" style="background:rgba(99,102,241,0.15);">
                        <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="text-4xl font-extrabold text-white">{{ $totalRuns }}</div>
                <div class="text-[11px] text-slate-400">{{ $activeRuns }} active, {{ $completedRuns }} completed</div>
            </div>

            <!-- Avg Bugs/Run -->
            <div class="metric-card rounded-2xl p-5 space-y-3"
                 style="background:rgba(245,158,11,0.1); border:1px solid rgba(245,158,11,0.25);">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold text-amber-400 uppercase tracking-wider">Avg Bugs/Run</span>
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center" style="background:rgba(245,158,11,0.15);">
                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                </div>
                <div class="text-4xl font-extrabold text-white">{{ $avgBugsPerRun }}</div>
                <div class="text-[11px] text-slate-400">Quality metric</div>
            </div>
        </div>

        <!-- DISTRIBUTION CHARTS -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Bug Status Distribution -->
            <div class="bg-[#111827] border border-slate-800/80 rounded-2xl p-6">
                <div class="flex items-center gap-2 mb-5">
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:rgba(239,68,68,0.15);">
                        <svg class="w-3.5 h-3.5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h2 class="text-sm font-bold text-white">Bug Status Distribution</h2>
                </div>
                <div class="space-y-3.5">
                    @foreach($bugsByStatus as $status => $count)
                        @php
                            $percentage = $totalBugs > 0 ? round($count / $totalBugs * 100) : 0;
                            $colorMap = [
                                'Open'           => ['bg' => 'rgba(239,68,68,0.7)',   'text' => '#fca5a5'],
                                'In Progress'    => ['bg' => 'rgba(245,158,11,0.7)', 'text' => '#fde68a'],
                                'Done in Review' => ['bg' => 'rgba(139,92,246,0.7)', 'text' => '#c4b5fd'],
                                'Closed'         => ['bg' => 'rgba(16,185,129,0.7)', 'text' => '#6ee7b7'],
                                'Reopened'       => ['bg' => 'rgba(236,72,153,0.7)', 'text' => '#f9a8d4'],
                            ];
                            $clr = $colorMap[$status] ?? ['bg' => 'rgba(99,102,241,0.7)', 'text' => '#a5b4fc'];
                        @endphp
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-xs font-medium" style="color:{{ $clr['text'] }}">{{ $status }}</span>
                                <div class="flex items-center gap-2">
                                    <span class="text-[11px] text-slate-500">{{ $percentage }}%</span>
                                    <span class="text-xs font-bold text-white">{{ $count }}</span>
                                </div>
                            </div>
                            <div class="w-full rounded-full h-1.5" style="background:rgba(255,255,255,0.06);">
                                <div class="h-1.5 rounded-full bar-fill" style="width: {{ $percentage }}%; background: {{ $clr['bg'] }};"></div>
                            </div>
                        </div>
                    @endforeach
                    @if($totalBugs === 0)
                        <p class="text-xs text-slate-500 text-center py-4">Belum ada data bug</p>
                    @endif
                </div>
            </div>

            <!-- Test Result Status -->
            <div class="bg-[#111827] border border-slate-800/80 rounded-2xl p-6">
                <div class="flex items-center gap-2 mb-5">
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:rgba(16,185,129,0.15);">
                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h2 class="text-sm font-bold text-white">Test Result Status</h2>
                </div>
                <div class="space-y-3.5">
                    @foreach($resultsByStatus as $status => $count)
                        @php
                            $percentage = $totalResults > 0 ? round($count / $totalResults * 100) : 0;
                            $resultColorMap = [
                                'Passed'   => ['bg' => 'rgba(16,185,129,0.7)',  'text' => '#6ee7b7'],
                                'Failed'   => ['bg' => 'rgba(239,68,68,0.7)',   'text' => '#fca5a5'],
                                'Blocked'  => ['bg' => 'rgba(245,158,11,0.7)', 'text' => '#fde68a'],
                                'Untested' => ['bg' => 'rgba(99,102,241,0.7)', 'text' => '#a5b4fc'],
                            ];
                            $rclr = $resultColorMap[$status] ?? ['bg' => 'rgba(107,114,128,0.7)', 'text' => '#9ca3af'];
                        @endphp
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-xs font-medium" style="color:{{ $rclr['text'] }}">{{ $status }}</span>
                                <div class="flex items-center gap-2">
                                    <span class="text-[11px] text-slate-500">{{ $percentage }}%</span>
                                    <span class="text-xs font-bold text-white">{{ $count }}</span>
                                </div>
                            </div>
                            <div class="w-full rounded-full h-1.5" style="background:rgba(255,255,255,0.06);">
                                <div class="h-1.5 rounded-full bar-fill" style="width: {{ $percentage }}%; background: {{ $rclr['bg'] }};"></div>
                            </div>
                        </div>
                    @endforeach
                    @if($totalResults === 0)
                        <p class="text-xs text-slate-500 text-center py-4">Belum ada data hasil test</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- RECENT ITEMS -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Recent Bugs -->
            <div class="bg-[#111827] border border-slate-800/80 rounded-2xl overflow-hidden">
                <div class="flex items-center justify-between p-5 border-b border-slate-800/80">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:rgba(239,68,68,0.15);">
                            <svg class="w-3.5 h-3.5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                            </svg>
                        </div>
                        <h2 class="text-sm font-bold text-white">Recent Bugs</h2>
                    </div>
                    <a href="{{ route('report.bug-history') }}" class="text-[10px] text-indigo-400 hover:text-indigo-300 font-semibold transition">Lihat Semua →</a>
                </div>
                <div class="divide-y divide-slate-800/60">
                    @forelse($recentBugs as $bug)
                        @php
                            $bugBadge = match($bug->status) {
                                'Open'        => ['bg' => 'rgba(239,68,68,0.12)',  'border' => 'rgba(239,68,68,0.3)',   'text' => '#fca5a5'],
                                'In Progress' => ['bg' => 'rgba(245,158,11,0.12)', 'border' => 'rgba(245,158,11,0.3)', 'text' => '#fde68a'],
                                'Closed'      => ['bg' => 'rgba(16,185,129,0.12)', 'border' => 'rgba(16,185,129,0.3)', 'text' => '#6ee7b7'],
                                default       => ['bg' => 'rgba(99,102,241,0.12)', 'border' => 'rgba(99,102,241,0.3)', 'text' => '#a5b4fc'],
                            };
                        @endphp
                        <a href="{{ route('report.bug-detail', $bug->id) }}"
                           class="flex items-center justify-between p-4 hover:bg-white/[0.02] transition group">
                            <div class="min-w-0">
                                <div class="text-sm font-semibold text-white truncate group-hover:text-indigo-300 transition">{{ $bug->title }}</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">#{{ $bug->id }} &bull; {{ $bug->created_at->format('d M Y') }}</div>
                            </div>
                            <span class="ml-3 px-2.5 py-1 rounded-lg text-[10px] font-bold whitespace-nowrap shrink-0"
                                  style="background:{{ $bugBadge['bg'] }}; color:{{ $bugBadge['text'] }}; border:1px solid {{ $bugBadge['border'] }};">
                                {{ $bug->status }}
                            </span>
                        </a>
                    @empty
                        <div class="p-10 text-center">
                            <div class="w-10 h-10 rounded-2xl flex items-center justify-center mx-auto mb-3" style="background:rgba(99,102,241,0.1);border:1px solid rgba(99,102,241,0.2);">
                                <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <p class="text-xs text-slate-500 font-medium">Belum ada bugs</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Test Runs -->
            <div class="bg-[#111827] border border-slate-800/80 rounded-2xl overflow-hidden">
                <div class="flex items-center justify-between p-5 border-b border-slate-800/80">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:rgba(99,102,241,0.15);">
                            <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h2 class="text-sm font-bold text-white">Recent Test Runs</h2>
                    </div>
                    <a href="{{ route('test-runs.index') }}" class="text-[10px] text-indigo-400 hover:text-indigo-300 font-semibold transition">Lihat Semua →</a>
                </div>
                <div class="divide-y divide-slate-800/60">
                    @forelse($recentRuns as $run)
                        @php
                            $runBadge = $run->status === 'Active'
                                ? ['bg' => 'rgba(99,102,241,0.12)',  'border' => 'rgba(99,102,241,0.3)',  'text' => '#a5b4fc']
                                : ['bg' => 'rgba(16,185,129,0.12)',  'border' => 'rgba(16,185,129,0.3)',  'text' => '#6ee7b7'];
                        @endphp
                        <a href="{{ route('test-runs.index') }}"
                           class="flex items-center justify-between p-4 hover:bg-white/[0.02] transition group">
                            <div class="min-w-0">
                                <div class="text-sm font-semibold text-white truncate group-hover:text-indigo-300 transition">{{ $run->title }}</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">{{ $run->project?->name ?? '-' }} &bull; {{ $run->created_at->format('d M Y') }}</div>
                            </div>
                            <span class="ml-3 px-2.5 py-1 rounded-lg text-[10px] font-bold whitespace-nowrap shrink-0"
                                  style="background:{{ $runBadge['bg'] }}; color:{{ $runBadge['text'] }}; border:1px solid {{ $runBadge['border'] }};">
                                {{ $run->status }}
                            </span>
                        </a>
                    @empty
                        <div class="p-10 text-center">
                            <div class="w-10 h-10 rounded-2xl flex items-center justify-center mx-auto mb-3" style="background:rgba(99,102,241,0.1);border:1px solid rgba(99,102,241,0.2);">
                                <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <p class="text-xs text-slate-500 font-medium">Belum ada test runs</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>

<x-profile-modal />
</body>
</html>
