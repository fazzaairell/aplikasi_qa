<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Suites & Test Cases - TESTIFY</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>* { font-family: 'Inter', sans-serif; } body { background: #0c0f1a; } ::-webkit-scrollbar{width:5px;height:5px} ::-webkit-scrollbar-track{background:#0c0f1a} ::-webkit-scrollbar-thumb{background:rgba(99,102,241,.3);border-radius:99px}</style>
</head>
<body class="h-full font-sans text-slate-100 flex overflow-hidden" x-data="{
    showAddSuiteModal: false,
    showAddCaseModal: false,
    showAddStep: false,
    showDeleteSuiteModal: false,
    showFromTemplateModal: false,
    activeSuiteId: null,
    activeSuiteName: '',
    selectedTestCaseId: null,
    sidebarOpen: false,
    collapsed: false,
    suiteMode: 'manual',
    selectedTemplateId: '',
    selectedTemplateProjectId: '{{ $selectedProjectId }}',
    editCase: {
        show: false,
        id: null,
        test_case_code: '',
        title: '',
        requirement_id: '',
        priority: 'High',
        expected_result: ''
    }
}">

    <x-sidebar />

    <!-- MAIN CONTENT -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto h-full" class="bg-[#0c0f1a]">

        <!-- TOPBAR -->
        <header class="h-16 border-b px-4 sm:px-8 flex items-center justify-between sticky top-0 z-30" style="background:rgba(12,15,26,.85);backdrop-filter:blur(12px);border-color:rgba(255,255,255,.06);">
            <div class="flex items-center space-x-4">
                <button @click="$dispatch('toggle-sidebar')" class="md:hidden text-slate-400 hover:text-white p-2 rounded-lg bg-[#131b2e] border border-slate-800 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <div class="w-32 sm:w-48 md:w-96">
                    <input type="text" id="searchInput" oninput="filterData()" placeholder="Cari proyek, test case, bug..." class="w-full px-4 py-2 bg-[#131b2e] border border-slate-800 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500">
                </div>
            </div>
            <div class="flex items-center space-x-4">
                @if($selectedProjectId)
                    <button @click="showAddSuiteModal = true" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs transition shadow-lg shadow-indigo-600/30 cursor-pointer">
                        + Test Suite Baru
                    </button>
                @endif
            </div>
        </header>

        <main class="p-8 space-y-6">
            <div>
                <div class="text-xs text-indigo-400 font-semibold tracking-wider mb-1">PENGUJIAN</div>
                <h1 class="text-3xl font-bold text-white tracking-tight">Test Suites & Test Cases</h1>
            </div>

            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-semibold">
                    {{ session('success') }}
                </div>
            @endif

            @if(!$selectedProjectId)
                <!-- DAFTAR PROYEK (GRID) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($projects as $p)
                        <a href="{{ route('test-suites.index', ['project_id' => $p->id]) }}" class="data-item block p-6 rounded-2xl bg-[#131b2e] border border-slate-800 hover:border-indigo-500/50 hover:bg-slate-800/50 transition group shadow-xl">
                            <h3 class="text-lg font-bold text-white group-hover:text-indigo-400 mb-2">{{ $p->name }}</h3>
                            <p class="text-xs text-slate-400 mb-4">{{ Str::limit($p->description, 80) ?: 'Tidak ada deskripsi' }}</p>
                            <div class="flex items-center justify-between text-[11px] font-semibold">
                                <span class="text-slate-500">{{ $p->test_suites_count ?? 0 }} Test Suites</span>
                                <span class="text-indigo-400 group-hover:underline">Kelola Test Suites &rarr;</span>
                            </div>
                        </a>
                    @empty
                        <div class="col-span-full p-8 text-center bg-[#131b2e] border border-slate-800 rounded-2xl text-slate-400 text-sm">
                            Belum ada proyek yang tersedia.
                        </div>
                    @endforelse
                </div>
            @else
                <!-- TAB PILIHAN PROYEK -->
                <div class="flex items-center space-x-3 overflow-x-auto pb-2">
                    <a href="{{ route('test-suites.index') }}" class="px-4 py-2.5 rounded-xl text-xs font-semibold transition border whitespace-nowrap bg-[#131b2e] text-slate-400 border-slate-800 hover:text-white">&larr; Semua Proyek</a>
                    @foreach($projects as $p)
                        <a href="{{ route('test-suites.index', ['project_id' => $p->id]) }}"
                           class="px-4 py-2.5 rounded-xl text-xs font-semibold transition border whitespace-nowrap {{ $selectedProjectId == $p->id ? 'bg-indigo-600 text-white border-indigo-500 shadow-lg shadow-indigo-600/30' : 'bg-[#131b2e] text-slate-400 border-slate-800 hover:text-white' }}">
                            {{ $p->name }}
                        </a>
                    @endforeach
                </div>

                <!-- LIST TEST SUITES & TEST CASES (ACCORDION) -->
                <div class="space-y-4">
                    @forelse($testSuites as $suite)
                        <div class="data-item bg-[#131b2e] border border-slate-800/80 rounded-2xl overflow-hidden shadow-xl" x-data="{ open: false }">

                            <!-- SUITE HEADER -->
                            <div @click="open = !open" class="p-5 flex items-center justify-between cursor-pointer hover:bg-slate-800/40 transition">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-500/20 text-indigo-400 flex items-center justify-center font-bold text-xs border border-indigo-500/30">
                                        {{ substr($suite->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <h2 class="text-sm font-bold text-white">{{ $suite->name }}</h2>
                                        <span class="text-[11px] text-slate-400">{{ $suite->testCases->count() }} test cases</span>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-4">
                                    <!-- Status Counter Badge -->
                                    <div class="flex items-center space-x-2">
                                        @php
                                            $passedCount = $suite->testCases->filter(fn($tc) => optional($tc->testResults->last())->status == 'Passed')->count();
                                            $failedCount = $suite->testCases->filter(fn($tc) => optional($tc->testResults->last())->status == 'Failed')->count();
                                            $blockedCount = $suite->testCases->filter(fn($tc) => optional($tc->testResults->last())->status == 'Blocked')->count();
                                            $untestedCount = $suite->testCases->count() - ($passedCount + $failedCount + $blockedCount);
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
                                        @if($untestedCount > 0 || ($passedCount == 0 && $failedCount == 0 && $blockedCount == 0))
                                            <span class="px-2.5 py-1 rounded-full bg-slate-700/30 border border-slate-700 text-slate-400 text-[10px] font-bold">{{ $untestedCount }} Untested</span>
                                        @endif
                                    </div>

                                    <!-- Tombol Simpan sebagai Template -->
                                    <form action="{{ route('test-suite-templates.save-as') }}" method="POST" class="inline" @click.stop>
                                        @csrf
                                        <input type="hidden" name="test_suite_id" value="{{ $suite->id }}">
                                        <button type="submit"
                                            onclick="return confirm('Simpan &quot;{{ addslashes($suite->name) }}&quot; sebagai Template? Semua {{ $suite->testCases->count() }} test case akan di-copy.')"
                                            title="Simpan sebagai Template"
                                            class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-violet-400 hover:text-violet-300 border border-violet-500/20 hover:border-violet-400/40 bg-violet-500/5 hover:bg-violet-500/10 text-[10px] font-semibold transition cursor-pointer">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                                            Template
                                        </button>
                                    </form>

                                    <!-- Hapus Suite -->
                                    <form action="{{ route('test-suites.destroy', $suite->id) }}" method="POST" class="inline" @click.stop>
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            onclick="return confirm('Hapus Test Suite &quot;{{ addslashes($suite->name) }}&quot;? Semua test case di dalamnya akan terhapus.')"
                                            title="Hapus Suite"
                                            class="p-1.5 rounded-lg text-slate-600 hover:text-red-400 hover:bg-red-500/10 transition cursor-pointer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>

                                    <!-- Arrow Toggle -->
                                    <svg class="w-5 h-5 text-slate-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>

                            <!-- TEST CASES TABLE -->
                            <div x-show="open" class="border-t border-slate-800/80 bg-[#0f1523]/50">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="border-b border-slate-800/60 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                                            <th class="py-3 px-4 w-20">Kode TC</th>
                                            <th class="py-3 px-6">Judul Test Case</th>
                                            <th class="py-3 px-6">Priority</th>
                                            <th class="py-3 px-6">Status</th>
                                            <th class="py-3 px-6">Requirement</th>
                                            <th class="py-3 px-6">Expected Result</th>
                                            <th class="py-3 px-6 text-right">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-800/40 text-xs">
                                        @forelse($suite->testCases as $tc)
                                            <tr class="hover:bg-slate-800/20 transition align-top">
                                                {{-- Kode TC --}}
                                                <td class="py-3.5 px-4 whitespace-nowrap">
                                                    @if($tc->test_case_code)
                                                        <span class="inline-block px-2 py-0.5 rounded-md bg-indigo-500/10 border border-indigo-500/20 text-indigo-300 text-[10px] font-bold font-mono tracking-wide">
                                                            {{ $tc->test_case_code }}
                                                        </span>
                                                    @else
                                                        <span class="text-slate-600 text-[10px]">&mdash;</span>
                                                    @endif
                                                </td>

                                                {{-- Judul & Sub-steps --}}
                                                <td class="py-3.5 px-6 font-medium text-slate-200">
                                                    <div class="font-semibold">{{ $tc->title }}</div>

                                                    <div
                                                        id="substeps-wrap-{{ $tc->id }}"
                                                        class="mt-3 mb-1 {{ $tc->subSteps->isEmpty() ? 'hidden' : '' }}"
                                                        x-data="{ open: false }"
                                                    >
                                                        <div class="flex items-center justify-between mb-2">
                                                            <span class="text-[9px] font-bold uppercase tracking-widest text-slate-600 flex items-center gap-1.5">
                                                                <span>Sub-steps</span>
                                                                <span class="text-slate-700">({{ $tc->subSteps->count() }})</span>
                                                            </span>

                                                            {{-- Toggle show/hide: ikon panah kecil saja --}}
                                                            <button
                                                                type="button"
                                                                @click.stop="open = !open"
                                                                title="Tampilkan / sembunyikan sub-steps"
                                                                class="p-1 rounded-md text-slate-600 hover:text-indigo-400 hover:bg-indigo-500/10 transition cursor-pointer"
                                                            >
                                                                <svg
                                                                    class="w-3.5 h-3.5 transition-transform duration-200"
                                                                    :class="{ 'rotate-180': open }"
                                                                    fill="none"
                                                                    stroke="currentColor"
                                                                    viewBox="0 0 24 24"
                                                                >
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                                </svg>
                                                            </button>
                                                        </div>

                                                        <div x-show="open" x-transition class="relative pl-3">
                                                            <div
                                                                class="absolute left-[7px] top-2 bottom-2 w-px"
                                                                style="background:rgba(99,102,241,0.25);"
                                                            ></div>

                                                            <div class="space-y-2" id="substeps-list-{{ $tc->id }}">
                                                                @foreach($tc->subSteps as $step)
                                                                    <div class="flex items-start gap-2.5 relative">
                                                                        <div
                                                                            class="w-[15px] h-[15px] rounded-full shrink-0 flex items-center justify-center text-[8px] font-bold mt-0.5 z-10"
                                                                            style="background:rgba(99,102,241,0.2);color:#a5b4fc;border:1px solid rgba(99,102,241,0.3);"
                                                                        >
                                                                            {{ $step->step_number }}
                                                                        </div>
                                                                        <div class="text-[10px] text-slate-400 leading-relaxed pt-0.5">
                                                                            {{ $step->description }}
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>

                                                {{-- Priority --}}
                                                <td class="py-3.5 px-6">
                                                    @php
                                                        $pColor = match($tc->priority) {
                                                            'Critical' => 'text-rose-400',
                                                            'High' => 'text-amber-400',
                                                            'Medium' => 'text-indigo-400',
                                                            default => 'text-slate-400 '
                                                        };
                                                    @endphp
                                                    <span class="font-semibold {{ $pColor }}">&uarr; {{ $tc->priority }}</span>
                                                </td>

                                                {{-- Status --}}
                                                <td class="py-3.5 px-6">
                                                    @php
                                                        $latestStatus = optional($tc->testResults->last())->status ?? 'Untested';
                                                        $sBadge = match($latestStatus) {
                                                            'Passed' => 'bg-emerald-500/10 border-emerald-500/20 text-emerald-400',
                                                            'Failed' => 'bg-red-500/10 border-red-500/20 text-red-400',
                                                            'Blocked' => 'bg-amber-500/10 border-amber-500/20 text-amber-400',
                                                            default => 'bg-slate-700/20 border-slate-700 text-slate-400 '
                                                        };
                                                    @endphp
                                                    <span class="px-2.5 py-1 rounded-full border text-[10px] font-bold {{ $sBadge }}">
                                                        {{ $latestStatus }}
                                                    </span>
                                                </td>

                                                {{-- Requirement --}}
                                                <td class="py-3.5 px-6 font-mono text-slate-400 text-[11px]">
                                                    {{ optional($tc->requirement)->code ?? '&mdash;' }}
                                                </td>

                                                {{-- Expected Result --}}
                                                <td class="py-3.5 px-6 text-slate-400 text-[10px] max-w-[180px]">
                                                    @if($tc->expected_result)
                                                        <span class="line-clamp-2 leading-relaxed">{{ Str::limit($tc->expected_result, 80) }}</span>
                                                    @else
                                                        <span class="text-slate-600">&mdash;</span>
                                                    @endif
                                                </td>

                                                {{-- Aksi --}}
                                                <td class="py-3.5 px-6 text-right">
                                                    <div class="flex items-center justify-end gap-1">

                                                        {{-- Sub-step --}}
                                                        <button type="button"
                                                            @click="showAddStep = true; selectedTestCaseId = '{{ $tc->id }}'; $dispatch('open-step-modal')"
                                                            title="Tambah Sub-step"
                                                            class="p-1.5 rounded-lg text-slate-500 hover:text-indigo-400 hover:bg-indigo-500/10 border border-transparent hover:border-indigo-500/20 transition cursor-pointer">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8M4 18h12"/>
                                                            </svg>
                                                        </button>

                                                        {{-- Edit --}}
                                                        <button type="button"
                                                            @click="editCase = {
                                                                show: true,
                                                                id: {{ $tc->id }},
                                                                test_case_code: '{{ addslashes($tc->test_case_code ?? '') }}',
                                                                title: '{{ addslashes($tc->title) }}',
                                                                requirement_id: '{{ $tc->requirement_id ?? '' }}',
                                                                priority: '{{ $tc->priority }}',
                                                                expected_result: '{{ addslashes($tc->expected_result ?? '') }}'
                                                            }"
                                                            title="Edit Test Case"
                                                            class="p-1.5 rounded-lg text-slate-500 hover:text-amber-400 hover:bg-amber-500/10 border border-transparent hover:border-amber-500/20 transition cursor-pointer">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                            </svg>
                                                        </button>

                                                        {{-- Hapus --}}
                                                        <form action="{{ route('test-cases.destroy', $tc->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus test case ini?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                title="Hapus Test Case"
                                                                class="p-1.5 rounded-lg text-slate-500 hover:text-red-400 hover:bg-red-500/10 border border-transparent hover:border-red-500/20 transition cursor-pointer">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                                </svg>
                                                            </button>
                                                        </form>

                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="py-6 text-center text-slate-500 text-xs">Belum ada test case dalam suite ini.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                <!-- TOMBOL TAMBAH TEST CASE -->
                                <div class="p-3 border-t border-slate-800/60 bg-[#0b0f19]/30 text-left">
                                    <button @click="activeSuiteId = '{{ $suite->id }}'; showAddCaseModal = true" class="text-indigo-400 hover:text-indigo-300 text-xs font-semibold flex items-center space-x-1 cursor-pointer">
                                        <span>+ Tambah Test Case</span>
                                    </button>
                                </div>
                            </div>

                        </div>
                    @empty
                        <div class="text-center py-16 text-slate-500 text-xs bg-[#131b2e] border border-slate-800 rounded-2xl">
                            Belum ada Test Suite yang terdaftar untuk proyek ini. Silakan buat Test Suite baru.
                        </div>
                    @endforelse
                </div>
            @endif
        </main>
    </div>

    <!-- MODAL TAMBAH TEST SUITE -->
    <div x-show="showAddSuiteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4" style="display: none;">
        <div @click.away="showAddSuiteModal = false" class="w-full max-w-lg p-6 rounded-2xl bg-[#131b2e] border border-slate-800 space-y-4 shadow-2xl mx-4 md:mx-0 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-sm font-bold text-white">Tambah Test Suite Baru</h3>
                <button @click="showAddSuiteModal = false" class="text-slate-400 hover:text-white text-lg font-bold cursor-pointer">&times;</button>
            </div>

            <!-- TAB SWITCHER -->
            <div class="flex rounded-xl overflow-hidden border border-slate-800 text-xs font-semibold">
                <button type="button"
                    @click="suiteMode = 'manual'"
                    :class="suiteMode === 'manual' ? 'bg-indigo-600 text-white' : 'bg-[#0b0f19] text-slate-400 hover:text-white'"
                    class="flex-1 py-2.5 transition cursor-pointer flex items-center justify-center gap-2">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Buat Manual
                </button>
                <button type="button"
                    @click="suiteMode = 'template'"
                    :class="suiteMode === 'template' ? 'bg-violet-600 text-white' : 'bg-[#0b0f19] text-slate-400 hover:text-white'"
                    class="flex-1 py-2.5 transition cursor-pointer flex items-center justify-center gap-2">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                    Mulai dari Template
                </button>
            </div>

            <!-- MODE: MANUAL -->
            <div x-show="suiteMode === 'manual'" x-transition>
                <form action="{{ route('test-suites.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="project_id" value="{{ $selectedProjectId }}">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 mb-1">Nama Test Suite</label>
                        <input type="text" name="name" required placeholder="Contoh: Authentication Suite" class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-indigo-500 text-xs">
                    </div>
                    <div class="flex items-center justify-end space-x-3 pt-2">
                        <button type="button" @click="showAddSuiteModal = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold transition cursor-pointer">Simpan Suite</button>
                    </div>
                </form>
            </div>

            <!-- MODE: DARI TEMPLATE -->
            <div x-show="suiteMode === 'template'" x-transition>
                @php
                    $allTemplates = \App\Models\TestSuiteTemplate::with('testCaseTemplates')
                        ->orderByDesc('created_at')->get();
                    $templatesJson = $allTemplates->map(fn($t) => [
                        'id'   => $t->id,
                        'name' => $t->name,
                        'tcs'  => $t->testCaseTemplates->map(fn($tc) => [
                            'id'             => $tc->id,
                            'code'           => $tc->test_case_code,
                            'title'          => $tc->title,
                            'priority'       => $tc->priority,
                        ])->values(),
                    ])->values()->toJson();
                @endphp

                @if($allTemplates->isEmpty())
                    <div class="text-center py-8 text-slate-500 text-xs">
                        <svg class="w-10 h-10 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                        Belum ada template tersimpan.<br>Simpan sebuah Test Suite sebagai template terlebih dahulu.
                    </div>
                @else
                    <div x-data="{
                            allTmpl: {{ $templatesJson }},
                            tmplId: '',
                            projectId: '{{ $selectedProjectId ?? '' }}',
                            templateTcs: [],
                            requirements: [],
                            reqMap: {},
                            loading: false,
                            onTemplateChange() {
                                const t = this.allTmpl.find(x => x.id == this.tmplId);
                                this.templateTcs = t ? t.tcs : [];
                                this.reqMap = {};
                            },
                            async onProjectChange() {
                                this.requirements = [];
                                if (!this.projectId) return;
                                this.loading = true;
                                try {
                                    const r = await fetch(`/projects/${this.projectId}/requirements-json`);
                                    this.requirements = await r.json();
                                } catch(e) { this.requirements = []; }
                                this.loading = false;
                            }
                        }"
                        x-init="if (projectId) onProjectChange()">

                        <form action="{{ route('test-suite-templates.use') }}" method="POST" class="space-y-4">
                            @csrf

                            <!-- Pilih Template -->
                            <div>
                                <label class="block text-[11px] font-bold text-slate-400 mb-1">Pilih Template</label>
                                <select name="template_id" x-model="tmplId" @change="onTemplateChange()" required
                                    class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-violet-500 text-xs">
                                    <option value="">-- Pilih Template --</option>
                                    @foreach($allTemplates as $tmpl)
                                        <option value="{{ $tmpl->id }}">{{ $tmpl->name }} ({{ $tmpl->testCaseTemplates->count() }} test cases)</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Pilih Project -->
                            <div>
                                <label class="block text-[11px] font-bold text-slate-400 mb-1">Project Tujuan</label>
                                <select name="project_id" x-model="projectId" @change="onProjectChange()" required
                                    class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-violet-500 text-xs">
                                    <option value="">-- Pilih Project --</option>
                                    @foreach($projects as $proj)
                                        <option value="{{ $proj->id }}" {{ $selectedProjectId == $proj->id ? 'selected' : '' }}>{{ $proj->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Nama Suite -->
                            <div>
                                <label class="block text-[11px] font-bold text-slate-400 mb-1">Nama Suite <span class="text-slate-600 font-normal">(opsional)</span></label>
                                <input type="text" name="suite_name" placeholder="Kosongkan untuk pakai nama template"
                                    class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-violet-500 text-xs">
                            </div>

                            <!-- Mapping Requirement per Test Case -->
                            <div x-show="templateTcs.length > 0" x-transition>
                                <div class="flex items-center gap-2 mb-2">
                                    <label class="text-[11px] font-bold text-slate-400">Mapping Requirement</label>
                                    <span class="text-[10px] text-slate-600">(opsional, per test case)</span>
                                    <div x-show="loading" class="ml-auto">
                                        <svg class="w-3.5 h-3.5 text-violet-400 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                                    </div>
                                </div>
                                <div class="rounded-xl border border-slate-800/60 overflow-hidden divide-y divide-slate-800/60">
                                    <template x-for="tc in templateTcs" :key="tc.id">
                                        <div class="px-3 py-2.5 bg-[#0b0f19]/50 flex items-center gap-3">
                                            <!-- Kode TC badge -->
                                            <span class="shrink-0 text-[9px] font-bold font-mono px-1.5 py-0.5 rounded bg-indigo-500/10 text-indigo-300 border border-indigo-500/20 min-w-[40px] text-center"
                                                x-text="tc.code || '&mdash;'"
                                            ></span>
                                            <!-- Priority badge -->
                                            <span class="shrink-0 text-[9px] font-bold px-1.5 py-0.5 rounded"
                                                :class="{
                                                    'bg-rose-500/15 text-rose-400': tc.priority === 'Critical',
                                                    'bg-amber-500/15 text-amber-400': tc.priority === 'High',
                                                    'bg-indigo-500/15 text-indigo-400': tc.priority === 'Medium',
                                                    'bg-slate-700/30 text-slate-400': tc.priority === 'Low'
                                                }"
                                                x-text="tc.priority">
                                            </span>
                                            <!-- TC Title -->
                                            <span class="text-[11px] text-slate-300 font-medium flex-1 truncate" x-text="tc.title"></span>
                                            <!-- Requirement dropdown -->
                                            <select
                                                :name="`requirement_ids[${tc.id}]`"
                                                x-model="reqMap[tc.id]"
                                                class="w-36 px-2 py-1.5 bg-[#131b2e] border border-slate-700 rounded-lg text-slate-300 text-[10px] focus:outline-none focus:border-violet-500 shrink-0">
                                                <option value="">&mdash; Tanpa Req &mdash;</option>
                                                <template x-for="req in requirements" :key="req.id">
                                                    <option :value="req.id" x-text="req.code + ' &mdash; ' + (req.description ? req.description.substring(0,30) : '')"></option>
                                                </template>
                                            </select>
                                        </div>
                                    </template>
                                </div>
                                <p x-show="requirements.length === 0 && projectId && !loading" class="text-[10px] text-slate-600 mt-1.5">
                                    Project ini belum memiliki requirement terdaftar.
                                </p>
                            </div>

                            <div class="p-3 rounded-xl bg-violet-500/5 border border-violet-500/20 text-[10px] text-violet-300 flex items-start gap-2">
                                <svg class="w-3.5 h-3.5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Test case di-<strong>copy</strong> secara independen &mdash; perubahan pada suite baru tidak mempengaruhi template asli.</span>
                            </div>

                            <div class="flex items-center justify-end space-x-3 pt-1">
                                <button type="button" @click="showAddSuiteModal = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition cursor-pointer">Batal</button>
                                <button type="submit" class="px-4 py-2 rounded-xl bg-violet-600 hover:bg-violet-500 text-white text-xs font-semibold transition cursor-pointer">Generate dari Template</button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- MODAL TAMBAH TEST CASE -->
    <div x-show="showAddCaseModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4" style="display: none;">
        <div @click.away="showAddCaseModal = false" class="w-full max-w-lg p-6 rounded-2xl bg-[#131b2e] border border-slate-800 space-y-4 shadow-2xl mx-4 md:mx-0 max-h-[85vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-sm font-bold text-white">Tambah Test Case Baru</h3>
                <button @click="showAddCaseModal = false" class="text-slate-400 hover:text-white text-lg font-bold cursor-pointer">&times;</button>
            </div>

            <form action="{{ route('test-cases.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="test_suite_id" x-model="activeSuiteId">

                {{-- Kode Test Case --}}
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 mb-1">
                        Kode Test Case
                        <span class="text-slate-600 font-normal">(opsional, misal: TC-001)</span>
                    </label>
                    <input type="text" name="test_case_code"
                        placeholder="Contoh: TC-001"
                        class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-indigo-500 text-xs font-mono placeholder-slate-600">
                </div>

                {{-- Judul Test Case --}}
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 mb-1">Judul Test Case</label>
                    <input type="text" name="title" required placeholder="Contoh: Login dengan kredensial valid"
                        class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-indigo-500 text-xs">
                </div>

                {{-- Requirement Terkait --}}
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 mb-1">Requirement Terkait</label>
                    <select name="requirement_id" class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-indigo-500 text-xs">
                        <option value="">Pilih Requirement (Opsional)</option>
                        @foreach($requirements as $req)
                            <option value="{{ $req->id }}">{{ $req->code }} - {{ Str::limit($req->description, 50) }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Priority --}}
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 mb-1">Priority</label>
                    <select name="priority" class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-indigo-500 text-xs">
                        <option value="Low">Low</option>
                        <option value="Medium">Medium</option>
                        <option value="High" selected>High</option>
                        <option value="Critical">Critical</option>
                    </select>
                </div>

                {{-- Expected Result --}}
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 mb-1">Expected Result</label>
                    <textarea name="expected_result" rows="3"
                        placeholder="Pengguna berhasil masuk ke dashboard..."
                        class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-indigo-500 text-xs resize-none"></textarea>
                </div>

                {{-- Info langkah uji via sub-steps --}}
                <div class="flex items-start gap-2 p-3 rounded-xl bg-indigo-500/5 border border-indigo-500/20 text-[10px] text-indigo-300">
                    <svg class="w-3.5 h-3.5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Langkah uji detail dapat ditambahkan melalui tombol <strong>Sub-step</strong> setelah test case disimpan.</span>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-2">
                    <button type="button" @click="showAddCaseModal = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold transition cursor-pointer">Simpan Test Case</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT TEST CASE -->
    <div x-show="editCase.show" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4" style="display: none;">
        <div @click.away="editCase.show = false" class="w-full max-w-lg p-6 rounded-2xl bg-[#131b2e] border border-amber-500/20 space-y-4 shadow-2xl mx-4 md:mx-0 max-h-[85vh] overflow-y-auto">

            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-amber-500/10 border border-amber-500/20 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-white">Edit Test Case</h3>
                        <p class="text-[10px] text-slate-500 mt-0.5" x-text="'ID #' + editCase.id"></p>
                    </div>
                </div>
                <button @click="editCase.show = false" class="text-slate-400 hover:text-white text-lg font-bold cursor-pointer">&times;</button>
            </div>

            <form :action="`{{ url('/test-cases') }}/${editCase.id}`" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                {{-- Kode Test Case --}}
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 mb-1">
                        Kode Test Case
                        <span class="text-slate-600 font-normal">(opsional, misal: TC-001)</span>
                    </label>
                    <input type="text" name="test_case_code"
                        x-model="editCase.test_case_code"
                        placeholder="Contoh: TC-001"
                        class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-amber-500 text-xs font-mono placeholder-slate-600">
                </div>

                {{-- Judul Test Case --}}
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 mb-1">Judul Test Case</label>
                    <input type="text" name="title" required
                        x-model="editCase.title"
                        placeholder="Contoh: Login dengan kredensial valid"
                        class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-amber-500 text-xs">
                </div>

                {{-- Requirement Terkait --}}
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 mb-1">Requirement Terkait</label>
                    <select name="requirement_id" x-model="editCase.requirement_id"
                        class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-amber-500 text-xs">
                        <option value="">Pilih Requirement (Opsional)</option>
                        @foreach($requirements as $req)
                            <option value="{{ $req->id }}">{{ $req->code }} - {{ Str::limit($req->description, 50) }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Priority --}}
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 mb-1">Priority</label>
                    <select name="priority" x-model="editCase.priority"
                        class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-amber-500 text-xs">
                        <option value="Low">Low</option>
                        <option value="Medium">Medium</option>
                        <option value="High">High</option>
                        <option value="Critical">Critical</option>
                    </select>
                </div>

                {{-- Expected Result --}}
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 mb-1">Expected Result</label>
                    <textarea name="expected_result" rows="3"
                        x-model="editCase.expected_result"
                        placeholder="Pengguna berhasil masuk ke dashboard..."
                        class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-amber-500 text-xs resize-none"></textarea>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-2">
                    <button type="button" @click="editCase.show = false"
                        class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition cursor-pointer">Batal</button>
                    <button type="submit"
                        class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-white text-xs font-bold transition cursor-pointer shadow-lg shadow-amber-500/20">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL TAMBAH SUB-STEP (MULTIPLE APPEND MODE) -->
    <div x-show="showAddStep" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4" style="display: none;">
        <div @click.away="showAddStep = false" class="w-full max-w-lg rounded-2xl bg-[#131b2e] border border-indigo-500/20 shadow-2xl mx-4 md:mx-0 overflow-hidden" style="max-height:90vh;">
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8M4 18h12"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-white">Tambah Langkah Uji</h3>
                        <p class="text-[10px] text-indigo-400 mt-0.5">Mode tambah beruntun &mdash; nomor urut otomatis</p>
                    </div>
                </div>
                <button @click="showAddStep = false" class="text-slate-400 hover:text-white text-xl font-bold cursor-pointer leading-none">&times;</button>
            </div>

            <!-- Preview daftar langkah yang sudah ditambahkan sesi ini -->
            <div id="stepPreviewPanel" class="hidden border-b border-slate-800/60 bg-[#0b0f19]/40 px-5 py-3">
                <p class="text-[9px] font-bold uppercase tracking-widest text-slate-600 mb-2">Langkah yang baru ditambahkan</p>
                <div id="stepPreviewList" class="space-y-1.5 max-h-32 overflow-y-auto"></div>
            </div>

            <!-- Form -->
            <div class="px-6 py-5">
                <form id="addStepForm" onsubmit="return submitAddStep(event)" class="space-y-4">
                    @csrf
                    <input type="hidden" name="test_case_id" x-model="selectedTestCaseId">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 mb-1">Deskripsi Langkah <span class="text-rose-400">*</span></label>
                        <textarea id="stepDescInput" name="description" rows="3" required
                            placeholder="Contoh: Masukkan username dan password yang valid, lalu klik tombol Login..."
                            class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-indigo-500 text-xs resize-none placeholder-slate-600"></textarea>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 mb-1">Expected Result <span class="text-slate-600 font-normal">(Opsional)</span></label>
                        <textarea id="stepExpectedInput" name="expected_result" rows="2"
                            placeholder="Contoh: Sistem menampilkan halaman dashboard..."
                            class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-indigo-500 text-xs resize-none placeholder-slate-600"></textarea>
                    </div>
                    <p id="addStepError" class="text-[11px] text-red-400 hidden"></p>
                    <div class="flex items-center justify-between pt-1">
                        <button type="button" @click="showAddStep = false"
                            class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition cursor-pointer">
                            Selesai
                        </button>
                        <button type="submit" id="addStepBtn"
                            class="flex items-center gap-2 px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold transition cursor-pointer shadow-lg shadow-indigo-600/20">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Tambah &amp; Lanjutkan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function submitAddStep(event) {
            event.preventDefault();
            const form = document.getElementById('addStepForm');
            const errorEl = document.getElementById('addStepError');
            const btn = document.getElementById('addStepBtn');
            const formData = new FormData(form);
            const testCaseId = formData.get('test_case_id');

            errorEl.classList.add('hidden');
            errorEl.textContent = '';

            // Loading state
            btn.disabled = true;
            btn.innerHTML = `<svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg> Menyimpan...`;

            fetch("{{ route('test-case-steps.store') }}", {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData,
            })
            .then(async (response) => {
                const data = await response.json().catch(() => null);
                if (!response.ok || !data || !data.step) {
                    throw new Error((data && data.message) ? data.message : 'Gagal menambahkan langkah uji.');
                }
                return data.step;
            })
            .then((step) => {
                // 1. Append ke daftar sub-step di halaman (tabel)
                const list = document.getElementById('substeps-list-' + testCaseId);
                const wrap = document.getElementById('substeps-wrap-' + testCaseId);
                if (list) {
                    const row = document.createElement('div');
                    row.className = 'flex items-start gap-2.5 relative';
                    row.innerHTML = `
                        <div class="w-[15px] h-[15px] rounded-full shrink-0 flex items-center justify-center text-[8px] font-bold mt-0.5 z-10"
                             style="background:rgba(99,102,241,0.2);color:#a5b4fc;border:1px solid rgba(99,102,241,0.3);">${step.step_number}</div>
                        <div class="text-[10px] text-slate-400 leading-relaxed pt-0.5"></div>
                    `;
                    row.querySelector('div.text-\\[10px\\]').textContent = step.description;
                    list.appendChild(row);
                }
                if (wrap) {
                    wrap.classList.remove('hidden');
                }

                // 2. Tambah ke preview panel di dalam modal
                const previewPanel = document.getElementById('stepPreviewPanel');
                const previewList  = document.getElementById('stepPreviewList');
                if (previewList) {
                    const previewRow = document.createElement('div');
                    previewRow.className = 'flex items-start gap-2 py-1';
                    previewRow.innerHTML = `
                        <span class="w-4 h-4 rounded-full shrink-0 flex items-center justify-center text-[8px] font-bold mt-0.5"
                              style="background:rgba(99,102,241,0.2);color:#a5b4fc;border:1px solid rgba(99,102,241,0.3);">${step.step_number}</span>
                        <span class="text-[10px] text-slate-300 leading-relaxed flex-1"></span>
                    `;
                    previewRow.querySelector('span.flex-1').textContent = step.description;
                    previewList.appendChild(previewRow);
                    previewList.scrollTop = previewList.scrollHeight;
                }
                if (previewPanel) {
                    previewPanel.classList.remove('hidden');
                }

                // 3. Reset input fields, biarkan modal tetap terbuka untuk append berikutnya
                document.getElementById('stepDescInput').value = '';
                document.getElementById('stepExpectedInput').value = '';
                document.getElementById('stepDescInput').focus();
            })
            .catch((err) => {
                errorEl.textContent = err.message || 'Gagal menambahkan langkah uji.';
                errorEl.classList.remove('hidden');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Tambah &amp; Lanjutkan`;
            });

            return false;
        }

        // Reset preview panel ketika membuka modal untuk test case yang berbeda
        window.addEventListener('open-step-modal', () => {
            const previewPanel = document.getElementById('stepPreviewPanel');
            const previewList  = document.getElementById('stepPreviewList');
            if (previewList) previewList.innerHTML = '';
            if (previewPanel) previewPanel.classList.add('hidden');
            const descInput = document.getElementById('stepDescInput');
            const expInput  = document.getElementById('stepExpectedInput');
            if (descInput) { descInput.value = ''; setTimeout(() => descInput.focus(), 100); }
            if (expInput)  expInput.value  = '';
            const errorEl = document.getElementById('addStepError');
            if (errorEl) { errorEl.textContent = ''; errorEl.classList.add('hidden'); }
        });

        function filterData() {
            const search = (document.getElementById('searchInput')?.value || '').toLowerCase().trim();
            const items = document.querySelectorAll('.data-item');
            items.forEach(item => {
                const match = search === '' || item.innerText.toLowerCase().includes(search);
                item.style.display = match ? '' : 'none';
            });
        }
    </script>
</body>
</html>