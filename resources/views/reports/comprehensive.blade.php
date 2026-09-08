<!DOCTYPE html>

<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


<title>Comprehensive Reports - TESTIFY</title>

<script src="https://cdn.tailwindcss.com"></script>

<link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
    rel="stylesheet"
>

<script
    defer
    src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"
></script>

<style>
    * {
        font-family: 'Inter', sans-serif;
        box-sizing: border-box;
    }

    body {
        background: #0c0f1a;
    }

    ::-webkit-scrollbar {
        width: 5px;
        height: 5px;
    }

    ::-webkit-scrollbar-track {
        background: #0c0f1a;
    }

    ::-webkit-scrollbar-thumb {
        background: rgba(99, 102, 241, 0.3);
        border-radius: 99px;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .fade-in {
        animation: fadeIn 0.4s ease both;
    }

    .metric-card {
        transition: transform 0.2s ease;
    }

    .metric-card:hover {
        transform: translateY(-2px);
    }

    .bar-fill {
        transition: width 0.6s cubic-bezier(.4, 0, .2, 1);
    }
</style>


</head>

<body
    class="h-full text-slate-100 flex overflow-hidden"
    x-data="{ sidebarOpen: false }"
>


<x-sidebar />

<!-- MAIN CONTENT -->
<div class="flex-1 flex flex-col min-w-0 overflow-y-auto h-full">

    <!-- TOPBAR -->
    <header
        class="h-16 border-b border-white/[0.06] px-4 sm:px-8 flex items-center justify-between sticky top-0 z-30"
        style="background: rgba(12,15,26,0.85); backdrop-filter: blur(12px);"
    >

    </header>


    <div class="p-8 space-y-6 fade-in max-w-7xl mx-auto w-full">

        <!-- PAGE HEADER -->
        <div class="mb-2">

            <h1 class="text-3xl font-bold text-white tracking-tight">
                Comprehensive Reports
            </h1>

            <p class="text-sm text-slate-400 mt-1">
                Analisis mendalam tentang status testing, bug, dan kualitas proyek.
            </p>

        </div>


        <!-- FILTER -->
        <div class="bg-[#111827] border border-slate-800/80 rounded-2xl p-5">

            <form
                method="GET"
                class="grid grid-cols-1 md:grid-cols-3 gap-4"
            >

                <div>

                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">
                        Filter Proyek
                    </label>

                    <select
                        name="project_id"
                        onchange="this.form.submit()"
                        class="w-full px-3 py-2.5 bg-[#0c0f1a] border border-indigo-500/20 rounded-xl text-xs text-white focus:outline-none focus:border-indigo-500 transition"
                    >

                        <option value="">
                            Semua Proyek
                        </option>

                        @foreach($projects as $p)

                            <option
                                value="{{ $p->id }}"
                                {{ request('project_id') == $p->id ? 'selected' : '' }}
                            >
                                {{ $p->name }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="flex items-end">

                    @if(request('project_id'))

                        <a
                            href="{{ route('reports.comprehensive') }}"
                            class="px-4 py-2.5 text-xs text-indigo-400 hover:text-indigo-300 font-semibold border border-indigo-500/20 rounded-xl transition"
                            style="background:rgba(99,102,241,0.05);"
                        >
                            Reset Filter
                        </a>

                    @endif

                </div>

            </form>

        </div>


        <!-- KEY METRICS -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

            <!-- Total Requirements -->
            <div class="metric-card bg-[#111827] border border-slate-800/80 rounded-2xl p-5">
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Total Requirement</p>
                <p class="text-2xl font-bold text-white">{{ $totalRequirements }}</p>
            </div>

            <!-- Coverage -->
            <div class="metric-card bg-[#111827] border border-slate-800/80 rounded-2xl p-5">
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Requirement Tercover</p>
                <p class="text-2xl font-bold text-white">{{ $requirementsWithTestCase }} <span class="text-sm text-slate-500 font-semibold">/ {{ $totalRequirements }}</span></p>
                <div class="mt-2 h-1.5 rounded-full bg-slate-800 overflow-hidden">
                    <div class="bar-fill h-full bg-indigo-500" style="width: 0%;" x-data="{ coverage: {{ $coveragePercent }} }" x-init="$el.style.width = coverage + '%'"></div>
                </div>
                <p class="text-[10px] text-indigo-400 font-bold mt-1">{{ $coveragePercent }}%</p>
            </div>

            <!-- Total Test Cases -->
            <div class="metric-card bg-[#111827] border border-slate-800/80 rounded-2xl p-5">
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Total Test Case</p>
                <p class="text-2xl font-bold text-white">{{ $totalTestCases }}</p>
            </div>

            <!-- Orphan Test Cases -->
            <div class="metric-card bg-[#111827] border border-slate-800/80 rounded-2xl p-5">
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Test Case Tanpa Requirement</p>
                <p class="text-2xl font-bold {{ $orphanTestCases->count() > 0 ? 'text-amber-400' : 'text-white' }}">{{ $orphanTestCases->count() }}</p>
            </div>

        </div>


        <!-- REQUIREMENT TRACEABILITY MATRIX -->
        <div class="mb-2 mt-8">
            <h2 class="text-lg font-bold text-white tracking-tight">Requirement Traceability Matrix</h2>
            <p class="text-xs text-slate-400 mt-1">Setiap requirement, test case yang mengujinya, dan riwayat status dari tiap test run.</p>
        </div>

        <div class="space-y-4">

            @php
                $currentProjectName = null;
            @endphp

            @forelse($requirements as $requirement)

                @if(!$selectedProject && $requirement->project?->name !== $currentProjectName)
                    @php $currentProjectName = $requirement->project?->name; @endphp
                    <div class="flex items-center gap-2 pt-2 pb-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-400"></span>
                        <span class="text-[11px] font-bold text-indigo-300 uppercase tracking-widest">{{ $currentProjectName ?? 'Tanpa Proyek' }}</span>
                    </div>
                @endif

                <div class="data-item bg-[#131b2e] border border-slate-800/80 rounded-2xl overflow-hidden shadow-xl" x-data="{ open: false }">

                    <!-- REQUIREMENT HEADER -->
                    <div @click="open = !open" class="p-4 flex items-center justify-between cursor-pointer hover:bg-slate-800/40 transition">

                        <div class="flex items-center space-x-4 min-w-10">
                            <div class="w-16 h-9 shrink-0 rounded-lg bg-indigo-500/20 text-indigo-400 flex items-center justify-center font-bold text-[11px] border border-indigo-500/30">
                                {{ $requirement->code }}
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-sm font-bold text-white truncate">{{ $requirement->title ?: $requirement->description }}</h3>
                                <span class="text-[11px] text-slate-400">{{ $requirement->testCases->count() }} test case</span>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3 shrink-0">
                            @if($requirement->testCases->isEmpty())
                                <span class="px-2.5 py-1 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-400 text-[10px] font-bold whitespace-nowrap">Belum Ada Test Case</span>
                            @else
                                @php
                                    $latestPerCase = $requirement->testCases->map(fn ($tc) => optional($tc->testResults->first())->status ?? 'Untested');
                                    $passedCount = $latestPerCase->filter(fn ($s) => $s === 'Passed')->count();
                                    $failedCount = $latestPerCase->filter(fn ($s) => $s === 'Failed')->count();
                                    $blockedCount = $latestPerCase->filter(fn ($s) => $s === 'Blocked')->count();
                                    $untestedCount = $latestPerCase->filter(fn ($s) => $s === 'Untested')->count();
                                @endphp
                                @if($passedCount > 0)
                                    <span class="px-2.5 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-bold">{{ $passedCount }} Passed</span>
                                @endif
                                @if($failedCount > 0)
                                    <span class="px-2.5 py-1 rounded-full bg-red-500/10 border border-red-500/20 text-red-400 text-[10px] font-bold">{{ $failedCount }} Failed</span>
                                @endif
                                @if($blockedCount > 0)
                                    <span class="px-2.5 py-1 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-400 text-[10px] font-bold">{{ $blockedCount }} Blocked</span>
                                @endif
                                @if($untestedCount > 0)
                                    <span class="px-2.5 py-1 rounded-full bg-slate-700/30 border border-slate-700 text-slate-400 text-[10px] font-bold">{{ $untestedCount }} Untested</span>
                                @endif
                            @endif

                            <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 text-slate-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>

                    </div>

                    <!-- TEST CASES DI BAWAH REQUIREMENT INI -->
                    <div x-show="open" class="border-t border-slate-800/80 divide-y divide-slate-800/60">

                        @forelse($requirement->testCases as $testCase)
                            <div class="p-5 pl-8">

                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="text-slate-600">&#8618;</span>
                                        <h4 class="text-xs font-bold text-slate-200 truncate">{{ $testCase->title }}</h4>
                                        @php
                                            $pColor = match($testCase->priority) {
                                                'High' => 'text-red-400',
                                                'Medium' => 'text-amber-400',
                                                default => 'text-slate-400',
                                            };
                                        @endphp
                                        <span class="text-[10px] font-semibold {{ $pColor }}">&uarr; {{ $testCase->priority }}</span>
                                    </div>
                                </div>

                                @forelse($testCase->testResults as $result)
                                    @php
                                        $sBadge = match($result->status) {
                                            'Passed' => 'bg-emerald-500/10 border-emerald-500/20 text-emerald-400',
                                            'Failed' => 'bg-red-500/10 border-red-500/20 text-red-400',
                                            'Blocked' => 'bg-amber-500/10 border-amber-500/20 text-amber-400',
                                            default => 'bg-slate-700/20 border-slate-700 text-slate-400',
                                        };
                                    @endphp
                                    <div class="flex items-center justify-between py-1.5 pl-5 text-[11px]">
                                        <span class="text-slate-500">
                                            {{ optional($result->testRun)->title ?? 'Test Run tidak diketahui' }}
                                            <span class="text-slate-700">&middot;</span>
                                            {{ $result->updated_at->translatedFormat('d M Y, H:i') }}
                                        </span>
                                        <span class="px-2.5 py-0.5 rounded-full border text-[10px] font-bold {{ $sBadge }}">{{ $result->status }}</span>
                                    </div>
                                @empty
                                    <div class="flex items-center justify-between py-1.5 pl-5 text-[11px]">
                                        <span class="text-slate-600">Belum pernah dijalankan di test run manapun</span>
                                        <span class="px-2.5 py-0.5 rounded-full border bg-slate-700/20 border-slate-700 text-slate-400 text-[10px] font-bold">Untested</span>
                                    </div>
                                @endforelse

                            </div>
                        @empty
                            <div class="p-5 pl-8 text-[11px] text-slate-500">
                                Belum ada test case untuk requirement ini.
                            </div>
                        @endforelse

                    </div>

                </div>

            @empty
                <div class="p-8 text-center bg-[#131b2e] border border-slate-800 rounded-2xl text-slate-400 text-sm">
                    Belum ada requirement yang tersedia.
                </div>
            @endforelse

        </div>


        <!-- TEST CASE TANPA REQUIREMENT -->
        @if($orphanTestCases->isNotEmpty())
            <div class="mb-2 mt-8">
                <h2 class="text-lg font-bold text-white tracking-tight">Test Case Tanpa Requirement</h2>
                <p class="text-xs text-slate-400 mt-1">Test case ini belum dikaitkan ke requirement mana pun (misal hasil generate dari template).</p>
            </div>

            <div class="space-y-4">
                @foreach($orphanTestCases as $testCase)
                    <div class="data-item bg-[#131b2e] border border-slate-800/80 rounded-2xl overflow-hidden shadow-xl" x-data="{ open: false }">

                        <div @click="open = !open" class="p-5 flex items-center justify-between cursor-pointer hover:bg-slate-800/40 transition">
                            <div class="min-w-0">
                                <h3 class="text-sm font-bold text-white truncate">{{ $testCase->title }}</h3>
                                <span class="text-[11px] text-slate-400">
                                    {{ optional($testCase->testSuite)->name ?? '-' }}
                                    <span class="text-slate-700">&middot;</span>
                                    {{ optional($testCase->testSuite?->project)->name ?? '-' }}
                                </span>
                            </div>
                            <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 text-slate-500 transition-transform shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>

                        <div x-show="open" class="border-t border-slate-800/80 p-5 pl-8">
                            @forelse($testCase->testResults as $result)
                                @php
                                    $sBadge = match($result->status) {
                                        'Passed' => 'bg-emerald-500/10 border-emerald-500/20 text-emerald-400',
                                        'Failed' => 'bg-red-500/10 border-red-500/20 text-red-400',
                                        'Blocked' => 'bg-amber-500/10 border-amber-500/20 text-amber-400',
                                        default => 'bg-slate-700/20 border-slate-700 text-slate-400',
                                    };
                                @endphp
                                <div class="flex items-center justify-between py-1.5 text-[11px]">
                                    <span class="text-slate-500">
                                        {{ optional($result->testRun)->title ?? 'Test Run tidak diketahui' }}
                                        <span class="text-slate-700">&middot;</span>
                                        {{ $result->updated_at->translatedFormat('d M Y, H:i') }}
                                    </span>
                                    <span class="px-2.5 py-0.5 rounded-full border text-[10px] font-bold {{ $sBadge }}">{{ $result->status }}</span>
                                </div>
                            @empty
                                <div class="text-[11px] text-slate-600">Belum pernah dijalankan di test run manapun.</div>
                            @endforelse
                        </div>

                    </div>
                @endforeach
            </div>
        @endif

    </div>

</div>


<x-profile-modal />


</body>

</html>
