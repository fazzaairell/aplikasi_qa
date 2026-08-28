<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Test Case - QA Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>* { font-family: 'Inter', sans-serif; } body { background: #0c0f1a; } ::-webkit-scrollbar{width:5px;height:5px} ::-webkit-scrollbar-track{background:#0c0f1a} ::-webkit-scrollbar-thumb{background:rgba(99,102,241,.3);border-radius:99px}</style>

</head>
<body class="h-full font-sans text-slate-100 flex overflow-hidden" x-data="{ showAddModal: false }">
    <x-sidebar />
<div class="flex-1 flex flex-col min-w-0 overflow-y-auto h-full" class="bg-[#0c0f1a]">
    <header class="h-16 border-b px-4 sm:px-8 flex items-center justify-between sticky top-0 z-30" style="background:rgba(12,15,26,.85);backdrop-filter:blur(12px);border-color:rgba(255,255,255,.06);">
        <div class="flex items-center space-x-4">
            <div class="w-32 sm:w-48 md:w-96">
                <input type="text" id="searchInput" oninput="filterData()" placeholder="Cari master test case..." class="w-full px-4 py-2 bg-[#131b2e] border border-slate-800 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500">
            </div>
        </div>
        <div class="flex items-center space-x-4">
            <button @click="showAddModal = true" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs transition shadow-lg shadow-indigo-600/30 cursor-pointer">
                + Master Test Case
            </button>
        </div>
    </header>

    <main class="p-8 space-y-6">
        <div>
            <div class="text-xs text-indigo-400 font-semibold tracking-wider mb-1">PENGUJIAN</div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Master Test Case</h1>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-semibold">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex items-center space-x-3 overflow-x-auto pb-2">
            @foreach($projects as $p)
                <a href="{{ route('master-test-cases.index', ['project_id' => $p->id]) }}"
                   class="px-4 py-2.5 rounded-xl text-xs font-semibold transition border whitespace-nowrap {{ $selectedProjectId == $p->id ? 'bg-indigo-600 text-white border-indigo-500 shadow-lg shadow-indigo-600/30' : 'bg-[#131b2e] text-slate-400 border-slate-800 hover:text-white' }}">
                    {{ $p->name }}
                </a>
            @endforeach
        </div>

        <div class="rounded-2xl overflow-x-auto shadow-xl" style="background:#111827;border:1px solid rgba(255,255,255,.06);">
            <table class="w-full text-left border-collapse min-w-[900px]">
                <thead>
                    <tr class="border-b border-slate-800 text-[11px] font-bold text-slate-400 uppercase tracking-wider bg-[#0f1523]">
                        <th class="py-4 px-6">Judul</th>
                        <th class="py-4 px-6">Requirement</th>
                        <th class="py-4 px-6">Priority</th>
                        <th class="py-4 px-6">Suite</th>
                        <th class="py-4 px-6">Steps</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 text-xs">
                    @forelse($masterTestCases as $case)
                        <tr class="data-item hover:bg-slate-800/30 transition align-top">
                            <td class="py-4 px-6 text-slate-200 font-semibold">{{ $case->title }}</td>
                            <td class="py-4 px-6 text-slate-300">{{ $case->requirement?->code ?? '-' }}</td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-1 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 text-[10px] font-bold">{{ $case->priority }}</span>
                            </td>
                            <td class="py-4 px-6 text-slate-300">
                                @forelse($case->testSuites as $suite)
                                    <span class="inline-block mr-2 mb-1 px-2 py-1 rounded-lg bg-slate-700/50 text-[10px]">{{ $suite->name }}</span>
                                @empty
                                    <span class="text-slate-500">-</span>
                                @endforelse
                            </td>
                            <td class="py-4 px-6 text-slate-300 whitespace-pre-line">{{ $case->steps }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-12 text-slate-500 text-xs">Belum ada master test case untuk proyek ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>
</div>

<div x-show="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4" style="display: none;">
    <div @click.away="showAddModal = false" class="w-full max-w-2xl p-6 rounded-2xl border space-y-4 shadow-2xl mx-4 md:mx-0 max-h-[85vh] overflow-y-auto" style="background:#111827;border-color:rgba(255,255,255,.08);">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h3 class="text-sm font-bold text-white">Tambah Master Test Case</h3>
            <button @click="showAddModal = false" class="text-slate-400 hover:text-white text-lg font-bold cursor-pointer">&times;</button>
        </div>

        <form action="{{ route('master-test-cases.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="project_id" value="{{ $selectedProjectId }}">
            <div>
                <label class="block text-[11px] font-bold text-slate-400 mb-1">Judul Test Case</label>
                <input type="text" name="title" required class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-indigo-500 text-xs">
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-400 mb-1">Requirement</label>
                <select name="requirement_id" class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-indigo-500 text-xs">
                    <option value="">Pilih Requirement</option>
                    @foreach($projects->find($selectedProjectId)?->requirements ?? [] as $req)
                        <option value="{{ $req->id }}">{{ $req->code }} - {{ Str::limit($req->description, 60) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-400 mb-1">Priority</label>
                <select name="priority" class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-indigo-500 text-xs">
                    <option value="Low">Low</option>
                    <option value="Medium" selected>Medium</option>
                    <option value="High">High</option>
                    <option value="Critical">Critical</option>
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-400 mb-1">Langkah-langkah</label>
                <textarea name="steps" rows="4" required class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-indigo-500 text-xs"></textarea>
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-400 mb-1">Expected Result</label>
                <textarea name="expected_result" rows="3" required class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-indigo-500 text-xs"></textarea>
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-400 mb-1">Tambahkan ke Test Suite</label>
                <select name="test_suite_id" class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-indigo-500 text-xs">
                    <option value="">Pilih Test Suite (opsional)</option>
                    @foreach($suites as $suite)
                        <option value="{{ $suite->id }}">{{ $suite->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center justify-end space-x-3 pt-2">
                <button type="button" @click="showAddModal = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition cursor-pointer">Batal</button>
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
