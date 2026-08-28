<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proyek - QA Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>* { font-family: 'Inter', sans-serif; } body { background: #0c0f1a; } ::-webkit-scrollbar{width:5px;height:5px} ::-webkit-scrollbar-track{background:#0c0f1a} ::-webkit-scrollbar-thumb{background:rgba(99,102,241,.3);border-radius:99px}</style>

</head>
<body class="h-full font-sans text-slate-100 flex overflow-hidden" x-data="{ 
    showAddModal: false, 
    showEditModal: false, 
    editForm: { id: '', name: '', description: '', status: 'Aktif', test_plan: { scope: '', schedule: '', pic: '', strategy: '' } },
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
                    <input type="text" id="searchInput" oninput="filterData()" placeholder="Cari proyek..." class="w-full px-4 py-2 bg-[#131b2e] border border-slate-800 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500">
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <button @click="showAddModal = true" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs transition shadow-lg shadow-indigo-600/30 cursor-pointer">
                    + Tambah Proyek
                </button>
            </div>
        </header>

        <main class="p-8 space-y-6">
            <div>
                <div class="text-xs text-indigo-400 font-semibold tracking-wider mb-1">MANAJEMEN</div>
                <h1 class="text-2xl font-bold text-white tracking-tight">Daftar Proyek</h1>
            </div>

            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-semibold">
                    {{ session('success') }}
                </div>
            @endif

            <!-- DAFTAR PROYEK (GRID / CARD) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($projects as $project)
                    <div class="data-item bg-[#131b2e] border border-slate-800/80 rounded-2xl p-6 flex flex-col justify-between space-y-4 shadow-xl hover:border-slate-700 hover:bg-[#17213a] hover:scale-105 transition duration-300 cursor-pointer">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="px-2.5 py-1 rounded-lg bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 font-mono text-[11px] font-bold">PROJECT</span>
                                <div class="flex items-center space-x-3">
                                    <a href="{{ route('projects.show', $project->id) }}" class="text-xs text-emerald-400 hover:text-emerald-300 font-semibold cursor-pointer">Detail</a>
                                    <button @click="showEditModal = true; editForm = { id: '{{ $project->id }}', name: '{{ $project->name }}', description: '{{ $project->description }}', status: '{{ $project->status ?? 'Aktif' }}', test_plan: {{ Js::from($project->test_plan ?? ['scope' => '', 'schedule' => '', 'pic' => '', 'strategy' => '']) }} }" class="text-xs text-slate-400 hover:text-indigo-400 font-semibold cursor-pointer">Edit</button>
                                    <form action="{{ route('projects.destroy', $project->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus proyek ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-red-400 hover:text-red-300 font-semibold cursor-pointer">Hapus</button>
                                    </form>
                                </div>
                            </div>
                            <h2 class="text-base font-bold text-white">{{ $project->name }}</h2>
                            <p class="text-xs text-slate-400 leading-relaxed">{{ $project->description }}</p>
                        </div>
                        <div class="pt-4 border-t border-slate-800/80 flex items-center justify-between">
                            <a href="{{ route('requirements.index', ['project_id' => $project->id]) }}" class="text-xs text-indigo-400 hover:underline font-semibold flex items-center space-x-1">
                                <span>Kelola Requirements &rarr;</span>
                            </a>
                            <span class="text-[10px] text-slate-500">{{ $project->created_at->format('d M Y') }}</span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12 text-slate-500 text-xs bg-[#131b2e] border border-slate-800 rounded-2xl">
                        Belum ada proyek yang terdaftar. Silakan buat proyek baru.
                    </div>
                @endforelse
            </div>
        </main>
    </div>

    <!-- MODAL TAMBAH PROYEK -->
    <div x-show="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4" style="display: none;">
        <div @click.away="showAddModal = false" class="w-full max-w-lg p-6 rounded-2xl bg-[#131b2e] border border-slate-800 space-y-4 shadow-2xl mx-4 md:mx-0 max-h-[85vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-sm font-bold text-white">Tambah Proyek Baru</h3>
                <button @click="showAddModal = false" class="text-slate-400 hover:text-white text-lg font-bold cursor-pointer">&times;</button>
            </div>
            
            <form action="{{ route('projects.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 mb-1">Nama Proyek</label>
                    <input type="text" name="name" required placeholder="Contoh: E-Commerce Platform" class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-indigo-500 text-xs">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 mb-1">Status</label>
                    <select name="status" class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-indigo-500 text-xs">
                        <option value="Aktif">Aktif</option>
                        <option value="Pending">Pending</option>
                        <option value="Selesai">Selesai</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 mb-1">Deskripsi</label>
                    <textarea name="description" rows="3" required placeholder="Deskripsi singkat mengenai proyek..." class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-indigo-500 text-xs"></textarea>
                </div>
                <div class="space-y-3 pt-1">
                    <h3 class="text-xs font-bold text-indigo-400 border-b border-slate-800/60 pb-2">Detail Test Plan</h3>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 mb-1">Scope</label>
                        <input type="text" name="test_plan[scope]" placeholder="Contoh: Modul Auth & Pembayaran" class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-indigo-500 text-xs">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 mb-1">Jadwal</label>
                        <input type="text" name="test_plan[schedule]" placeholder="Contoh: 12 - 20 Agustus 2026" class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-indigo-500 text-xs">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 mb-1">PIC</label>
                        <input type="text" name="test_plan[pic]" placeholder="Contoh: John Doe" class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-indigo-500 text-xs">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 mb-1">Strategi Test</label>
                        <input type="text" name="test_plan[strategy]" placeholder="Contoh: Manual & Automation" class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-indigo-500 text-xs">
                    </div>
                </div>
                <div class="flex items-center justify-end space-x-3 pt-2">
                    <button type="button" @click="showAddModal = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold transition cursor-pointer">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT PROYEK -->
    <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4" style="display: none;">
        <div @click.away="showEditModal = false" class="w-full max-w-lg p-6 rounded-2xl bg-[#131b2e] border border-slate-800 space-y-4 shadow-2xl mx-4 md:mx-0 max-h-[85vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-sm font-bold text-white">Edit Proyek</h3>
                <button @click="showEditModal = false" class="text-slate-400 hover:text-white text-lg font-bold cursor-pointer">&times;</button>
            </div>
            
            <form :action="'/projects/' + editForm.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 mb-1">Nama Proyek</label>
                    <input type="text" name="name" x-model="editForm.name" required class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-indigo-500 text-xs">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 mb-1">Status</label>
                    <select name="status" x-model="editForm.status" class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-indigo-500 text-xs">
                        <option value="Aktif">Aktif</option>
                        <option value="Pending">Pending</option>
                        <option value="Selesai">Selesai</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 mb-1">Deskripsi</label>
                    <textarea name="description" rows="3" x-model="editForm.description" required class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-indigo-500 text-xs"></textarea>
                </div>
                <div class="space-y-3 pt-1">
                    <h3 class="text-xs font-bold text-indigo-400 border-b border-slate-800/60 pb-2">Detail Test Plan</h3>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 mb-1">Scope</label>
                        <input type="text" name="test_plan[scope]" x-model="editForm.test_plan.scope" placeholder="Contoh: Modul Auth & Pembayaran" class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-indigo-500 text-xs">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 mb-1">Jadwal</label>
                        <input type="text" name="test_plan[schedule]" x-model="editForm.test_plan.schedule" placeholder="Contoh: 12 - 20 Agustus 2026" class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-indigo-500 text-xs">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 mb-1">PIC</label>
                        <input type="text" name="test_plan[pic]" x-model="editForm.test_plan.pic" placeholder="Contoh: John Doe" class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-indigo-500 text-xs">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 mb-1">Strategi Test</label>
                        <input type="text" name="test_plan[strategy]" x-model="editForm.test_plan.strategy" placeholder="Contoh: Manual & Automation" class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-indigo-500 text-xs">
                    </div>
                </div>
                <div class="flex items-center justify-end space-x-3 pt-2">
                    <button type="button" @click="showEditModal = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold transition cursor-pointer">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
<x-profile-modal />
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