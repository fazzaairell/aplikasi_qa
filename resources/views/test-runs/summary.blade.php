<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Summary: {{ $testRun->title }} - QA Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        body { background: #0c0f1a; }
        ::-webkit-scrollbar { width: 5px; } ::-webkit-scrollbar-track { background: #0c0f1a; }
        ::-webkit-scrollbar-thumb { background: rgba(99,102,241,.3); border-radius: 99px; }

        /* PRINT STYLES */
        @media print {
            body { background: #fff !important; color: #111 !important; }
            .no-print { display: none !important; }
            .print-card { background: #f8fafc !important; border: 1px solid #e2e8f0 !important; color: #111 !important; }
            .print-table th { background: #f1f5f9 !important; color: #475569 !important; }
            .print-table td { color: #1e293b !important; border-bottom: 1px solid #e2e8f0 !important; }
            .print-header { color: #1e293b !important; }
            .verdict-ready { background: #dcfce7 !important; color: #15803d !important; }
            .verdict-notready { background: #fee2e2 !important; color: #b91c1c !important; }
        }
    </style>

</head>
<body class="min-h-full text-slate-100">

{{-- TOPBAR --}}
<header class="h-14 border-b sticky top-0 z-40 no-print" style="background:rgba(12,15,26,.9);backdrop-filter:blur(12px);border-color:rgba(255,255,255,.06);">
    <div class="px-6 h-full flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('test-runs.index') }}" class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div class="w-7 h-7 rounded-lg flex items-center justify-center font-bold text-white text-xs"
                 style="background:linear-gradient(135deg,#4f46e5,#6366f1);">QA</div>
            <span class="text-sm font-semibold text-white">Test Summary Report</span>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()"
                    class="flex items-center gap-2 px-3.5 py-2 rounded-xl text-white text-xs font-semibold transition cursor-pointer no-print"
                    style="background:linear-gradient(135deg,#4f46e5,#6366f1);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print / Export PDF
            </button>
        </div>
    </div>
</header>

<main class="max-w-5xl mx-auto px-6 py-8 space-y-6">

    {{-- ═══════ HEADER REPORT ═══════ --}}
    <div class="print-header">
        <div class="text-[10px] text-indigo-400 font-bold tracking-widest uppercase mb-1">TEST SUMMARY REPORT</div>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-white">{{ $testRun->title }}</h1>
                <div class="flex items-center gap-3 mt-1 text-xs text-slate-400">
                    @if($testRun->project)
                    <span>📁 {{ $testRun->project->name }}</span>
                    @endif
                    <span>🗓 {{ $testRun->created_at->format('d M Y') }}</span>
                </div>
            </div>

            {{-- BADGE KESIMPULAN --}}
            @if($isReady)
            <div class="verdict-ready flex items-center gap-2 px-4 py-2.5 rounded-2xl font-bold text-sm"
                 style="background:rgba(16,185,129,0.15); color:#34d399; border:2px solid rgba(16,185,129,0.4);">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                ✅ SIAP RILIS
            </div>
            @else
            <div class="verdict-notready flex items-center gap-2 px-4 py-2.5 rounded-2xl font-bold text-sm"
                 style="background:rgba(239,68,68,0.15); color:#f87171; border:2px solid rgba(239,68,68,0.4);">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
                ❌ BELUM SIAP RILIS
            </div>
            @endif
        </div>
    </div>

    {{-- ═══════ KARTU STATISTIK ═══════ --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
        {{-- Pass Rate --}}
        <div class="col-span-2 sm:col-span-3 lg:col-span-1 rounded-2xl p-5 flex flex-col justify-center items-center print-card"
             style="background:rgba(99,102,241,0.15); border:1px solid rgba(99,102,241,0.3);">
            <div class="text-4xl font-extrabold text-white">{{ $passRate }}%</div>
            <div class="text-xs font-semibold mt-1" style="color:#a5b4fc">Pass Rate</div>
        </div>

        <div class="rounded-2xl p-4 space-y-1 print-card"
             style="background:rgba(16,185,129,0.12); border:1px solid rgba(16,185,129,0.25);">
            <div class="text-2xl font-bold text-white">{{ $passed }}</div>
            <div class="text-xs font-semibold" style="color:#6ee7b7">Passed</div>
        </div>

        <div class="rounded-2xl p-4 space-y-1 print-card"
             style="background:rgba(239,68,68,0.12); border:1px solid rgba(239,68,68,0.25);">
            <div class="text-2xl font-bold text-white">{{ $failed }}</div>
            <div class="text-xs font-semibold" style="color:#fca5a5">Failed</div>
        </div>

        <div class="rounded-2xl p-4 space-y-1 print-card"
             style="background:rgba(234,179,8,0.12); border:1px solid rgba(234,179,8,0.25);">
            <div class="text-2xl font-bold text-white">{{ $blocked }}</div>
            <div class="text-xs font-semibold" style="color:#fde047">Blocked</div>
        </div>

        <div class="rounded-2xl p-4 space-y-1 print-card"
             style="background:rgba(100,116,139,0.12); border:1px solid rgba(100,116,139,0.25);">
            <div class="text-2xl font-bold text-white">{{ $untested }}</div>
            <div class="text-xs font-semibold" style="color:#94a3b8">Untested</div>
        </div>
    </div>

    {{-- ═══════ PROGRESS BAR ═══════ --}}
    @if($total > 0)
    <div class="rounded-2xl p-4 print-card" style="background:#111827; border:1px solid rgba(255,255,255,0.06);">
        <div class="flex items-center justify-between text-xs text-slate-400 mb-2">
            <span>Test Execution Progress</span>
            <span>{{ $total }} test cases</span>
        </div>
        <div class="h-2 rounded-full overflow-hidden" style="background:rgba(255,255,255,0.06);">
            @php
                $passedW  = $total > 0 ? ($passed  / $total) * 100 : 0;
                $failedW  = $total > 0 ? ($failed  / $total) * 100 : 0;
                $blockedW = $total > 0 ? ($blocked / $total) * 100 : 0;
                $untestedW= $total > 0 ? ($untested/ $total) * 100 : 0;
            @endphp
            <div class="h-full flex">
                <div style="width:{{ $passedW }}%; background:#10b981;"></div>
                <div style="width:{{ $failedW }}%; background:#ef4444;"></div>
                <div style="width:{{ $blockedW }}%; background:#eab308;"></div>
                <div style="width:{{ $untestedW }}%; background:#374151;"></div>
            </div>
        </div>
        <div class="flex gap-4 mt-2">
            <span class="text-[10px] text-slate-500 flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-500"></span>Passed</span>
            <span class="text-[10px] text-slate-500 flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-red-500"></span>Failed</span>
            <span class="text-[10px] text-slate-500 flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-yellow-500"></span>Blocked</span>
            <span class="text-[10px] text-slate-500 flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-slate-700"></span>Untested</span>
        </div>
    </div>
    @endif

    {{-- ═══════ TABEL BUG ═══════ --}}
    <div>
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm font-bold text-white">Bug Ditemukan ({{ $bugs->count() }})</h2>
        </div>

        @if($bugs->isEmpty())
        <div class="rounded-2xl p-10 text-center print-card" style="background:#111827; border:1px solid rgba(255,255,255,0.06);">
            <div class="text-slate-400 text-sm">🎉 Tidak ada bug yang ditemukan dalam test run ini!</div>
        </div>
        @else
        <div class="rounded-2xl border overflow-hidden print-card" style="background:#111827; border-color:rgba(255,255,255,0.06);">
            <div class="overflow-x-auto">
                <table class="w-full text-xs print-table" style="min-width: 650px;">
                    <thead>
                        <tr class="border-b" style="background:rgba(12,15,26,0.6); border-color:rgba(255,255,255,0.06);">
                            <th class="px-4 py-3 text-left text-slate-400 font-semibold uppercase tracking-wider w-10">No</th>
                            <th class="px-4 py-3 text-left text-slate-400 font-semibold uppercase tracking-wider min-w-[200px]">Judul Bug</th>
                            <th class="px-4 py-3 text-left text-slate-400 font-semibold uppercase tracking-wider">Test Case</th>
                            <th class="px-4 py-3 text-left text-slate-400 font-semibold uppercase tracking-wider">Priority</th>
                            <th class="px-4 py-3 text-left text-slate-400 font-semibold uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-slate-400 font-semibold uppercase tracking-wider">Assigned To</th>
                            <th class="px-4 py-3 text-left text-slate-400 font-semibold uppercase tracking-wider">Finish Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $n = 0; @endphp
                        @foreach($bugs as $bug)
                        @php
                            $n++;
                            $priority = $bug->testResult?->testCase?->priority ?? 'Low';
                            $caseName = $bug->testResult?->testCase?->title ?? '—';
                        @endphp
                        <tr class="border-b" style="border-color:rgba(255,255,255,0.04);">
                            <td class="px-4 py-3 text-slate-500 font-mono">{{ $n }}</td>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-white">{{ $bug->title }}</div>
                                @if($bug->description)
                                <div class="text-slate-500 text-[10px] mt-0.5 truncate max-w-xs">{{ $bug->description }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-300 whitespace-nowrap">{{ $caseName }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($priority === 'Critical')
                                    <span class="px-2 py-0.5 rounded text-[9px] font-bold" style="background:rgba(239,68,68,0.12);color:#fca5a5;">🔴 Critical</span>
                                @elseif($priority === 'High')
                                    <span class="px-2 py-0.5 rounded text-[9px] font-bold" style="background:rgba(249,115,22,0.12);color:#fdba74;">🟠 High</span>
                                @elseif($priority === 'Medium')
                                    <span class="px-2 py-0.5 rounded text-[9px] font-bold" style="background:rgba(234,179,8,0.12);color:#fde047;">🟡 Medium</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[9px] font-bold" style="background:rgba(100,116,139,0.12);color:#94a3b8;">⬜ Low</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @php
                                    $sc = match($bug->status) {
                                        'Open'           => 'color:#fca5a5;background:rgba(239,68,68,0.12);',
                                        'In Progress'    => 'color:#a5b4fc;background:rgba(99,102,241,0.12);',
                                        'Done in Review' => 'color:#d8b4fe;background:rgba(168,85,247,0.12);',
                                        'Resolved'       => 'color:#6ee7b7;background:rgba(16,185,129,0.12);',
                                        'Closed'         => 'color:#94a3b8;background:rgba(100,116,139,0.12);',
                                        'Reopened'       => 'color:#fdba74;background:rgba(249,115,22,0.12);',
                                        default          => 'color:#94a3b8;background:rgba(100,116,139,0.12);',
                                    };
                                @endphp
                                <span class="px-2 py-0.5 rounded text-[9px] font-bold" style="{{ $sc }}">{{ $bug->status }}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-slate-300">
                                {{ $bug->assignee?->name ?? '—' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($bug->finish_date)
                                <span class="text-emerald-400 font-mono text-[10px]">{{ $bug->finish_date->format('d M Y') }}</span>
                                @else
                                <span class="text-slate-600">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>

    {{-- FOOTER --}}
    <div class="text-center text-[11px] text-slate-600 pb-6">
        Laporan dihasilkan oleh QA Platform · {{ now()->format('d M Y, H:i') }}
    </div>

</main>
</body>
</html>
