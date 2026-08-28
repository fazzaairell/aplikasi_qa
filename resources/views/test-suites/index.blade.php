<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Suites & Test Cases - QA Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>* { font-family: 'Inter', sans-serif; } body { background: #0c0f1a; } ::-webkit-scrollbar{width:5px;height:5px} ::-webkit-scrollbar-track{background:#0c0f1a} ::-webkit-scrollbar-thumb{background:rgba(99,102,241,.3);border-radius:99px}</style>

</head>
<body class="h-full font-sans text-slate-100 flex overflow-hidden" x-data="{ 
    showAddSuiteModal: false, 
    showAddCaseModal: false, 
    showAddStep: false,
    activeSuiteId: null,
    selectedTestCaseId: null,
    sidebarOpen: false,
    collapsed: false
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
                    <a href="{{ route('master-test-cases.index', ['project_id' => $selectedProjectId]) }}" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-semibold text-xs transition cursor-pointer">
                        + Master Test Case
                    </a>
                    <button @click="showAddSuiteModal = true" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs transition shadow-lg shadow-indigo-600/30 cursor-pointer">
                        + Test Suite Baru
                    </button>
                @endif
            </div>
        </header>

        <main class="p-8 space-y-6">
            <div>
                <div class="text-xs text-indigo-400 font-semibold tracking-wider mb-1">PENGUJIAN</div>
                <h1 class="text-2xl font-bold text-white tracking-tight">Test Suites & Test Cases</h1>
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

                                <!-- Arrow Toggle -->
                                <svg class="w-5 h-5 text-slate-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>

                        <!-- TEST CASES TABLE -->
                        <div x-show="open" class="border-t border-slate-800/80 bg-[#0f1523]/50">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-slate-800/60 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                                        <th class="py-3 px-6">Test Case</th>
                                        <th class="py-3 px-6">Priority</th>
                                        <th class="py-3 px-6">Status</th>
                                        <th class="py-3 px-6">Requirement</th>
                                        <th class="py-3 px-6 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800/40 text-xs">
                                    @forelse($suite->testCases as $tc)
                                        <tr class="hover:bg-slate-800/20 transition align-top">
                                            <td class="py-3.5 px-6 font-medium text-slate-200">
                                                <div class="font-semibold">{{ $tc->title }}</div>
                                                @if($tc->subSteps->isNotEmpty())
                                                    <div class="mt-2 space-y-1">
                                                        @foreach($tc->subSteps as $step)
                                                            <div class="text-[10px] text-slate-400 bg-slate-800/40 rounded px-2 py-1">
                                                                <span class="font-bold text-indigo-300">{{ $step->step_number }}.</span> {{ $step->description }}
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </td>
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
                                            <td class="py-3.5 px-6 font-mono text-slate-400 text-[11px]">
                                                {{ optional($tc->requirement)->code ?? '-' }}
                                            </td>
                                            <td class="py-3.5 px-6 text-right space-x-2">
                                                <button type="button" @click="showAddStep = true; selectedTestCaseId = '{{ $tc->id }}'" class="text-indigo-400 hover:text-indigo-300 text-[11px] font-semibold cursor-pointer">Sub-step</button>
                                                <form action="{{ route('test-cases.destroy', $tc->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus test case ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-slate-500 hover:text-red-400 text-[11px] font-semibold cursor-pointer">Hapus</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="py-6 text-center text-slate-500 text-xs">Belum ada test case dalam suite ini.</td>
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
        <div @click.away="showAddSuiteModal = false" class="w-full max-w-lg p-6 rounded-2xl bg-[#131b2e] border border-slate-800 space-y-4 shadow-2xl mx-4 md:mx-0 max-h-[85vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-sm font-bold text-white">Tambah Test Suite Baru</h3>
                <button @click="showAddSuiteModal = false" class="text-slate-400 hover:text-white text-lg font-bold cursor-pointer">&times;</button>
            </div>
            
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
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 mb-1">Judul Test Case</label>
                    <input type="text" name="title" required placeholder="Contoh: Login dengan kredensial valid" class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-indigo-500 text-xs">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 mb-1">Pilih Master Test Case (Opsional)</label>
                    <select name="master_test_case_id" class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-indigo-500 text-xs">
                        <option value="">Pilih master test case yang bisa dipakai ulang</option>
                        @foreach(App\Models\MasterTestCase::where('project_id', $selectedProjectId)->get() as $masterCase)
                            <option value="{{ $masterCase->id }}">{{ $masterCase->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 mb-1">Requirement Terkait</label>
                    <select name="requirement_id" class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-indigo-500 text-xs">
                        <option value="">Pilih Requirement (Opsional)</option>
                        @foreach($requirements as $req)
                            <option value="{{ $req->id }}">{{ $req->code }} - {{ Str::limit($req->description, 50) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 mb-1">Priority</label>
                    <select name="priority" class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-indigo-500 text-xs">
                        <option value="Low">Low</option>
                        <option value="Medium">Medium</option>
                        <option value="High" selected>High</option>
                        <option value="Critical">Critical</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 mb-1">Langkah-langkah (Steps)</label>
                    <textarea name="steps" rows="2" required placeholder="1. Buka halaman login..." class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-indigo-500 text-xs"></textarea>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 mb-1">Expected Result</label>
                    <textarea name="expected_result" rows="2" required placeholder="Pengguna berhasil masuk ke dashboard..." class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-indigo-500 text-xs"></textarea>
                </div>
                <div class="flex items-center justify-end space-x-3 pt-2">
                    <button type="button" @click="showAddCaseModal = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold transition cursor-pointer">Simpan Test Case</button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="showAddStep" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4" style="display: none;">
        <div @click.away="showAddStep = false" class="w-full max-w-lg p-6 rounded-2xl bg-[#131b2e] border border-slate-800 space-y-4 shadow-2xl mx-4 md:mx-0 max-h-[85vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-sm font-bold text-white">Tambah Langkah Uji</h3>
                <button @click="showAddStep = false" class="text-slate-400 hover:text-white text-lg font-bold cursor-pointer">&times;</button>
            </div>

            <form action="{{ route('test-case-steps.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="test_case_id" x-model="selectedTestCaseId">
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 mb-1">Nomor Langkah</label>
                    <input type="number" name="step_number" min="1" value="1" required class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-indigo-500 text-xs">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 mb-1">Deskripsi Langkah</label>
                    <textarea name="description" rows="3" required class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-indigo-500 text-xs"></textarea>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 mb-1">Expected Result</label>
                    <textarea name="expected_result" rows="2" required class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-indigo-500 text-xs"></textarea>
                </div>
                <div class="flex items-center justify-end space-x-3 pt-2">
                    <button type="button" @click="showAddStep = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold transition cursor-pointer">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
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