<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requirements - QA Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>* { font-family: 'Inter', sans-serif; } body { background: #0c0f1a; } ::-webkit-scrollbar{width:5px;height:5px} ::-webkit-scrollbar-track{background:#0c0f1a} ::-webkit-scrollbar-thumb{background:rgba(99,102,241,.3);border-radius:99px}</style>
</head>
<body class="h-full font-sans text-slate-100 flex overflow-hidden" x-data="{ 
    showAddModal: false, 
    showEditModal: false, 
    editForm: { id: '', code: '', description: '' },
    sidebarOpen: false,
    collapsed: false

}">
    <x-sidebar />
    <!-- MAIN CONTENT -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto h-full" style="background:#0c0f1a;">
        
        <!-- TOPBAR -->
        <header class="h-16 border-b px-4 sm:px-8 flex items-center justify-between sticky top-0 z-30" style="background:rgba(12,15,26,.85);backdrop-filter:blur(12px);border-color:rgba(255,255,255,.06);">
            <div class="flex items-center space-x-4">
                <button @click="$dispatch('toggle-sidebar')" class="md:hidden text-slate-400 hover:text-white p-2 rounded-lg bg-[#131b2e] border border-slate-800 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <div class="w-32 sm:w-48 md:w-96">
                    <input type="text" placeholder="Cari proyek, test case, bug..." class="w-full px-4 py-2 bg-[#131b2e] border border-slate-800 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500">
                </div>
            </div>
            <div class="flex items-center space-x-4">
                @if($selectedProjectId)
                    <button @click="showAddModal = true" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs transition shadow-lg shadow-indigo-600/30 cursor-pointer">
                        + Tambah Requirement
                    </button>
                @endif
            </div>
        </header>

        <main class="p-8 space-y-6">   
            <div>
                <div class="text-xs text-indigo-400 font-semibold tracking-wider mb-1">RTM</div>
                <h1 class="text-2xl font-bold text-white tracking-tight">Requirements</h1>
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
                        <a href="{{ route('requirements.index', ['project_id' => $p->id]) }}" class="block p-6 rounded-2xl bg-[#131b2e] border border-slate-800 hover:border-indigo-500/50 hover:bg-slate-800/50 transition group shadow-xl">
                            <h3 class="text-lg font-bold text-white group-hover:text-indigo-400 mb-2">{{ $p->name }}</h3>
                            <p class="text-xs text-slate-400 mb-4">{{ Str::limit($p->description, 80) ?: 'Tidak ada deskripsi' }}</p>
                            <div class="flex items-center justify-between text-[11px] font-semibold">
                                <span class="text-slate-500">{{ $p->requirements_count ?? 0 }} Requirements</span>
                                <span class="text-indigo-400 group-hover:underline">Kelola Requirements &rarr;</span>
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
                <a href="{{ route('requirements.index') }}" class="px-4 py-2.5 rounded-xl text-xs font-semibold transition border whitespace-nowrap bg-[#131b2e] text-slate-400 border-slate-800 hover:text-white">&larr; Semua Proyek</a>
                @foreach($projects as $p)
                    <a href="{{ route('requirements.index', ['project_id' => $p->id]) }}" 
                       class="px-4 py-2.5 rounded-xl text-xs font-semibold transition border whitespace-nowrap {{ $selectedProjectId == $p->id ? 'bg-indigo-600 text-white border-indigo-500 shadow-lg shadow-indigo-600/30' : 'bg-[#131b2e] text-slate-400 border-slate-800 hover:text-white' }}">
                        {{ $p->name }}
                    </a>
                @endforeach
            </div>

            <!-- TABEL REQUIREMENTS -->
            <div class="rounded-2xl overflow-x-auto shadow-xl" style="background:#111827;border:1px solid rgba(255,255,255,.06);">
                <table class="w-full text-left border-collapse min-w-[600px]">
                    <thead>
                        <tr class="border-b border-slate-800 text-[11px] font-bold text-slate-400 uppercase tracking-wider bg-[#0f1523]">
                            <th class="py-4 px-6 w-36">Kode</th>
                            <th class="py-4 px-6">Deskripsi</th>
                            <th class="py-4 px-6 w-32">Test Cases</th>
                            <th class="py-4 px-6 text-right w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 text-xs">
                        @forelse($requirements as $req)
                            <tr class="hover:bg-slate-800/30 transition">
                                <td class="py-4 px-6 font-mono font-bold text-indigo-400 whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-lg bg-indigo-500/10 border border-indigo-500/25 inline-block">{{ $req->code }}</span>
                                </td>
                                <td class="py-4 px-6 text-slate-300">{{ $req->description }}</td>
                                <td class="py-4 px-6 text-slate-400 font-medium whitespace-nowrap">
                                    {{ $req->testCases->count() }} cases
                                </td>
                                <td class="py-4 px-6 text-right space-x-3 whitespace-nowrap">
                                    <button @click="showEditModal = true; editForm = { id: '{{ $req->id }}', code: '{{ $req->code }}', description: '{{ $req->description }}' }" class="text-indigo-400 hover:text-indigo-300 font-semibold cursor-pointer">Edit</button>
                                    
                                    <form action="{{ route('requirements.destroy', $req->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus requirement ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-300 font-semibold cursor-pointer">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-12 text-slate-500 text-xs">Belum ada requirement untuk proyek ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @endif
        </main>
    </div>

    <!-- MODAL TAMBAH REQUIREMENT -->
    <div x-show="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4" style="display: none;">
        <div @click.away="showAddModal = false" class="w-full max-w-lg p-6 rounded-2xl border space-y-4 shadow-2xl mx-4 md:mx-0 max-h-[85vh] overflow-y-auto" style="background:#111827;border-color:rgba(255,255,255,.08);">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-sm font-bold text-white">Tambah Requirement Baru</h3>
                <button @click="showAddModal = false" class="text-slate-400 hover:text-white text-lg font-bold cursor-pointer">&times;</button>
            </div>
            
            <form action="{{ route('requirements.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="project_id" value="{{ $selectedProjectId }}">
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 mb-1">Kode Requirement</label>
                    <input type="text" name="code" required placeholder="Contoh: REQ-005" class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-indigo-500 text-xs font-mono">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 mb-1">Deskripsi</label>
                    <textarea name="description" rows="3" required placeholder="Contoh: Pengguna dapat memfilter produk berdasarkan kategori..." class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-indigo-500 text-xs"></textarea>
                </div>
                <div class="flex items-center justify-end space-x-3 pt-2">
                    <button type="button" @click="showAddModal = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold transition cursor-pointer">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT REQUIREMENT -->
    <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4" style="display: none;">
        <div @click.away="showEditModal = false" class="w-full max-w-lg p-6 rounded-2xl border space-y-4 shadow-2xl mx-4 md:mx-0 max-h-[85vh] overflow-y-auto" style="background:#111827;border-color:rgba(255,255,255,.08);">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-sm font-bold text-white">Edit Requirement</h3>
                <button @click="showEditModal = false" class="text-slate-400 hover:text-white text-lg font-bold cursor-pointer">&times;</button>
            </div>
            
            <form :action="'/requirements/' + editForm.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 mb-1">Kode Requirement</label>
                    <input type="text" name="code" x-model="editForm.code" required class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-indigo-500 text-xs font-mono">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 mb-1">Deskripsi</label>
                    <textarea name="description" rows="3" x-model="editForm.description" required class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-indigo-500 text-xs"></textarea>
                </div>
                <div class="flex items-center justify-end space-x-3 pt-2">
                    <button type="button" @click="showEditModal = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold transition cursor-pointer">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
<x-profile-modal />
</body>
</html>