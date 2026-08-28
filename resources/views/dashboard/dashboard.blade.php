<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - QA Management</title>
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
        .stat-card { transition: transform 0.2s, border-color 0.2s; }
        .stat-card:hover { transform: translateY(-2px); }
    </style>

</head>
<body class="h-full text-slate-100 flex overflow-hidden" x-data="{ sidebarOpen: false }">

<x-sidebar />

    <!-- MAIN CONTENT -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto h-full">

        <!-- TOPBAR -->
        <header class="h-16 border-b border-white/[0.06] px-4 sm:px-8 flex items-center justify-between sticky top-0 z-30"
                style="background:rgba(12,15,26,0.85); backdrop-filter:blur(12px);">
            <div class="flex items-center gap-3">
                <button @click="$dispatch('toggle-sidebar')"
                        class="md:hidden p-2 rounded-xl text-slate-400 hover:text-white border border-white/[0.06] cursor-pointer"
                        style="background:#111827;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <div class="mb-4">
                    <div class="text-[10px] text-indigo-400 font-bold tracking-widest uppercase pt-5">Overview</div>
                    <div class="text-sm font-bold text-white">Dashboard Admin</div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-[11px] text-slate-500">{{ now()->translatedFormat('d F Y') }}</span>
            </div>
        </header>

        <!-- DASHBOARD BODY -->
        <main class="p-4 sm:p-6 lg:p-8 space-y-6 fade-in">

            <!-- STAT CARDS (4 kartu) -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

                <!-- Total Proyek -->
                <div class="stat-card rounded-2xl p-5 relative overflow-hidden"
                     style="background:#111827; border:1px solid rgba(99,102,241,0.2);">
                    <div class="absolute top-4 right-4 w-10 h-10 rounded-xl flex items-center justify-center"
                         style="background:rgba(99,102,241,0.12); border:1px solid rgba(99,102,241,0.25);">
                        <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                        </svg>
                    </div>
                    <div class="text-3xl font-extrabold text-white mb-1">{{ $totalProjects }}</div>
                    <div class="text-xs font-semibold text-slate-300">Total Proyek</div>
                    <div class="text-[10px] text-slate-500 mt-0.5">aktif berjalan</div>
                </div>

                <!-- Pass Rate -->
                <div class="stat-card rounded-2xl p-5 relative overflow-hidden"
                     style="background:#111827; border:1px solid rgba(16,185,129,0.2);">
                    <div class="absolute top-4 right-4 w-10 h-10 rounded-xl flex items-center justify-center"
                         style="background:rgba(16,185,129,0.12); border:1px solid rgba(16,185,129,0.25);">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div class="text-3xl font-extrabold text-white mb-1">{{ $passRate }}%</div>
                    <div class="text-xs font-semibold text-slate-300">Pass Rate</div>
                    <div class="text-[10px] text-slate-500 mt-0.5">dari seluruh test</div>
                </div>

                <!-- Bug Aktif -->
                <div class="stat-card rounded-2xl p-5 relative overflow-hidden"
                     style="background:#111827; border:1px solid rgba(249,115,22,0.2);">
                    <div class="absolute top-4 right-4 w-10 h-10 rounded-xl flex items-center justify-center"
                         style="background:rgba(249,115,22,0.12); border:1px solid rgba(249,115,22,0.25);">
                        <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="text-3xl font-extrabold text-white mb-1">{{ $activeBugs }}</div>
                    <div class="text-xs font-semibold text-slate-300">Bug Aktif</div>
                    <div class="text-[10px] text-slate-500 mt-0.5">open / in progress</div>
                </div>

                <!-- Blocked -->
                <div class="stat-card rounded-2xl p-5 relative overflow-hidden"
                     style="background:#111827; border:1px solid rgba(239,68,68,0.2);">
                    <div class="absolute top-4 right-4 w-10 h-10 rounded-xl flex items-center justify-center"
                         style="background:rgba(239,68,68,0.12); border:1px solid rgba(239,68,68,0.25);">
                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                        </svg>
                    </div>
                    <div class="text-3xl font-extrabold text-white mb-1">{{ $blockedCount }}</div>
                    <div class="text-xs font-semibold text-slate-300">Blocked</div>
                    <div class="text-[10px] text-slate-500 mt-0.5">test tertunda</div>
                </div>
            </div>

            <!-- MIDDLE: Test Runs + Bug Terbaru -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

                <!-- Test Runs Aktif -->
                <div class="lg:col-span-2 rounded-2xl p-5 sm:p-6 space-y-4"
                     style="background:#111827; border:1px solid rgba(255,255,255,0.06);">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-bold text-white">Test Run Aktif</h3>
                        <span class="flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold"
                              style="background:rgba(99,102,241,0.1); color:#a5b4fc; border:1px solid rgba(99,102,241,0.2);">
                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 animate-pulse"></span>
                            Live
                        </span>
                    </div>

                    <div class="space-y-3">
                        @forelse($activeTestRuns as $run)
                        @php
                            $total   = $run->testResults->count();
                            $passed  = $run->testResults->where('status','Passed')->count();
                            $failed  = $run->testResults->where('status','Failed')->count();
                            $blocked = $run->testResults->where('status','Blocked')->count();
                            $untested = $total - $passed - $failed - $blocked;
                            $pct     = $total > 0 ? round(($passed / $total) * 100) : 0;

                            // Nilai style dihitung dulu di sini supaya atribut style="" di bawah
                            // hanya berisi variabel polos (tidak ada tanda kutip/ternary di dalamnya)
                            $runDotColor  = $run->status === 'Active' ? '#10b981' : '#64748b';
                            $passedWidth  = $total > 0 ? round($passed / $total * 100) : 0;
                            $failedWidth  = $total > 0 ? round($failed / $total * 100) : 0;
                            $blockedWidth = $total > 0 ? round($blocked / $total * 100) : 0;
                        @endphp
                        <div class="rounded-xl p-4 space-y-3" style="background:#0c0f1a; border:1px solid rgba(255,255,255,0.05);">
                            <div class="flex items-start justify-between">
                                <div>
                                    <div class="text-sm font-bold text-white">{{ $run->name }}</div>
                                    <div class="text-[11px] text-slate-500">{{ $run->project->name ?? '—' }}</div>
                                </div>
                                <span class="w-2 h-2 rounded-full mt-1.5 shrink-0"
                                      style="background:{{ $runDotColor }};"></span>
                            </div>
                            <div class="flex flex-wrap gap-2 text-[11px] font-bold">
                                <span class="text-emerald-400">{{ $passed }} Passed</span>
                                <span class="text-slate-600">•</span>
                                <span class="text-rose-400">{{ $failed }} Failed</span>
                                @if($blocked > 0)
                                <span class="text-slate-600">•</span>
                                <span class="text-amber-400">{{ $blocked }} Blocked</span>
                                @endif
                                <span class="text-slate-600">•</span>
                                <span class="text-slate-400">{{ $untested }} Untested</span>
                            </div>
                            <div class="w-full h-1.5 rounded-full overflow-hidden flex" style="background:#1e293b;">
                                @if($total > 0)
                                <div class="h-full bg-emerald-500" style="width:{{ $passedWidth }}%"></div>
                                <div class="h-full bg-rose-500"    style="width:{{ $failedWidth }}%"></div>
                                <div class="h-full bg-amber-500"   style="width:{{ $blockedWidth }}%"></div>
                                @endif
                            </div>
                            <div class="text-[10px] text-slate-500">{{ $pct }}% selesai · {{ $untested }} belum dieksekusi</div>
                        </div>
                        @empty
                        <div class="rounded-xl p-8 text-center" style="background:#0c0f1a; border:1px solid rgba(255,255,255,0.05);">
                            <div class="text-slate-500 text-sm">Tidak ada test run aktif</div>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Bug Terbaru -->
                <div class="rounded-2xl p-5 sm:p-6 space-y-4"
                     style="background:#111827; border:1px solid rgba(255,255,255,0.06);">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-bold text-white">Bug Terbaru</h3>
                        <a href="{{ route('bugs.index') }}"
                           class="text-[11px] font-semibold text-indigo-400 hover:text-indigo-300 transition">Lihat semua →</a>
                    </div>
                    <div class="space-y-2.5">
                        @forelse($recentBugs as $bug)
                        @php
                            $statusColors = [
                                'Open'        => ['bg'=>'rgba(239,68,68,0.1)','text'=>'#fca5a5','border'=>'rgba(239,68,68,0.2)'],
                                'In Progress' => ['bg'=>'rgba(99,102,241,0.1)','text'=>'#a5b4fc','border'=>'rgba(99,102,241,0.2)'],
                                'Resolved'    => ['bg'=>'rgba(16,185,129,0.1)','text'=>'#6ee7b7','border'=>'rgba(16,185,129,0.2)'],
                                'Closed'      => ['bg'=>'rgba(100,116,139,0.1)','text'=>'#94a3b8','border'=>'rgba(100,116,139,0.2)'],
                                'Reopened'    => ['bg'=>'rgba(168,85,247,0.1)','text'=>'#d8b4fe','border'=>'rgba(168,85,247,0.2)'],
                            ];
                            $sc = $statusColors[$bug->status] ?? $statusColors['Open'];

                            // Pecah array jadi variabel polos untuk dipakai di style=""
                            $scBg     = $sc['bg'];
                            $scText   = $sc['text'];
                            $scBorder = $sc['border'];
                        @endphp
                        <div class="rounded-xl p-3 space-y-2" style="background:#0c0f1a; border:1px solid rgba(255,255,255,0.05);">
                            <div class="flex items-start gap-2">
                                <span class="w-1.5 h-1.5 rounded-full mt-1.5 shrink-0"
                                      style="background:{{ $scText }};"></span>
                                <span class="text-xs font-semibold text-white leading-snug line-clamp-2">{{ $bug->title }}</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold"
                                      style="background:{{ $scBg }};color:{{ $scText }};border:1px solid {{ $scBorder }};">
                                    {{ $bug->status }}
                                </span>
                            </div>
                        </div>
                        @empty
                        <div class="rounded-xl p-6 text-center" style="background:#0c0f1a; border:1px solid rgba(255,255,255,0.05);">
                            <div class="text-slate-500 text-sm">Tidak ada bug</div>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- RINGKASAN PROYEK -->
            <div class="rounded-2xl p-5 sm:p-6 space-y-4"
                 style="background:#111827; border:1px solid rgba(255,255,255,0.06);">
                <h3 class="text-sm font-bold text-white">Ringkasan Proyek</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left" style="min-width:500px;">
                        <thead>
                            <tr class="border-b border-white/[0.06] text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                <th class="py-3 px-4">Proyek</th>
                                <th class="py-3 px-4">Test Cases</th>
                                <th class="py-3 px-4">Pass Rate</th>
                                <th class="py-3 px-4">Status</th>
                            </tr>
                        </thead>
                        <tbody class="text-xs divide-y divide-white/[0.04]">
                            @forelse($projects as $project)
                            @php
                                $tc       = $project->test_cases_count ?? 0;
                                $pPassed  = \DB::table('test_results')
                                    ->join('test_cases','test_results.test_case_id','=','test_cases.id')
                                    ->join('test_suites','test_cases.test_suite_id','=','test_suites.id')
                                    ->where('test_suites.project_id', $project->id)
                                    ->where('test_results.status','Passed')
                                    ->count();
                                $pTotal   = \DB::table('test_results')
                                    ->join('test_cases','test_results.test_case_id','=','test_cases.id')
                                    ->join('test_suites','test_cases.test_suite_id','=','test_suites.id')
                                    ->where('test_suites.project_id', $project->id)
                                    ->count();
                                $pr = $pTotal > 0 ? round($pPassed/$pTotal*100) : 0;
                                $prColor = $pr >= 80 ? '#10b981' : ($pr >= 60 ? '#f59e0b' : '#ef4444');
                                $initial = strtoupper(substr($project->name, 0, 1));
                                $colors = ['#4f46e5','#0891b2','#059669','#d97706','#7c3aed'];
                                $bgColor = $colors[crc32($project->name) % count($colors)];
                            @endphp
                            <tr class="hover:bg-white/[0.02] transition">
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-xl font-bold text-white flex items-center justify-center text-xs shrink-0"
                                             style="background:{{ $bgColor }};">{{ $initial }}</div>
                                        <div>
                                            <div class="font-semibold text-white">{{ $project->name }}</div>
                                            <div class="text-[11px] text-slate-500 mt-0.5">{{ Str::limit($project->description ?? '', 45) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4 font-mono font-bold text-slate-300">{{ $tc }}</td>
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-24 h-1.5 rounded-full overflow-hidden" style="background:#1e293b;">
                                            <div class="h-full rounded-full" style="width:{{ $pr }}%; background:{{ $prColor }};"></div>
                                        </div>
                                        <span class="font-bold text-xs" style="color:{{ $prColor }};">{{ $pr }}%</span>
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="px-2 py-1 rounded-lg text-[10px] font-bold"
                                          style="background:rgba(16,185,129,0.1);color:#6ee7b7;border:1px solid rgba(16,185,129,0.2);">
                                        Active
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="py-12 text-center text-slate-500 text-sm">Belum ada proyek.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

<x-profile-modal />
</body>
</html>