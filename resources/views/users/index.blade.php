<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pengguna - QA Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>* { font-family: 'Inter', sans-serif; } body { background: #0c0f1a; } ::-webkit-scrollbar{width:5px;height:5px} ::-webkit-scrollbar-track{background:#0c0f1a} ::-webkit-scrollbar-thumb{background:rgba(99,102,241,.3);border-radius:99px}</style>
</head>
<body class="h-full font-sans text-slate-100 flex overflow-hidden" x-data="{ 
    showModal: false, 
    user: { id: '', name: '', email: '', role: 'QA Tester', password: '' }, 
    isEdit: false,
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
                <button @click="showModal = true; isEdit = false; user = { id: '', name: '', email: '', role: 'QA Tester', password: '' }" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs transition shadow-lg shadow-indigo-600/30 cursor-pointer">
                    + Undang Pengguna
                </button>
            </div>
        </header>

        <main class="p-8 space-y-6">
            <div>
                <div class="text-xs text-indigo-400 font-semibold tracking-wider mb-1">TIM</div>
                <h1 class="text-2xl font-bold text-white tracking-tight">Manajemen Pengguna</h1>
            </div>

            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-semibold">
                    {{ session('success') }}
                </div>
            @endif

            <!-- KARTU STATISTIK ROLE -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-[#131b2e] border border-slate-800/80 rounded-2xl p-5 text-center shadow-lg">
                    <div class="text-3xl font-extrabold text-white mb-1">{{ $counts['Admin'] }}</div>
                    <div class="text-xs font-medium text-slate-400">Admin</div>
                </div>
                <div class="bg-[#131b2e] border border-slate-800/80 rounded-2xl p-5 text-center shadow-lg">
                    <div class="text-3xl font-extrabold text-white mb-1">{{ $counts['QA Lead'] }}</div>
                    <div class="text-xs font-medium text-slate-400">QA Lead</div>
                </div>
                <div class="bg-[#131b2e] border border-slate-800/80 rounded-2xl p-5 text-center shadow-lg">
                    <div class="text-3xl font-extrabold text-white mb-1">{{ $counts['QA Tester'] }}</div>
                    <div class="text-xs font-medium text-slate-400">QA Tester</div>
                </div>
                <div class="bg-[#131b2e] border border-slate-800/80 rounded-2xl p-5 text-center shadow-lg">
                    <div class="text-3xl font-extrabold text-white mb-1">{{ $counts['Developer'] }}</div>
                    <div class="text-xs font-medium text-slate-400">Developer</div>
                </div>
            </div>

            <!-- TABEL DAFTAR PENGGUNA -->
            <div class="bg-[#131b2e] border border-slate-800/80 rounded-2xl overflow-hidden shadow-xl">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-800 text-[11px] font-bold text-slate-400 uppercase tracking-wider bg-[#0f1523]">
                            <th class="py-4 px-6">Pengguna</th>
                            <th class="py-4 px-6">Email</th>
                            <th class="py-4 px-6">Role</th>
                            <th class="py-4 px-6">Proyek</th>
                            <th class="py-4 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 text-xs">
                        @forelse($users as $u)
                        @php
                            $initials = collect(explode(' ', $u->name))->map(fn($seg) => mb_substr($seg, 0, 1))->take(2)->join('');
                            
                            $roleBadgeClass = match($u->role) {
                                'Admin' => 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/30',
                                'QA Lead' => 'bg-blue-500/10 text-blue-400 border border-blue-500/30',
                                'QA Tester' => 'bg-teal-500/10 text-teal-400 border border-teal-500/30',
                                'Developer' => 'bg-amber-500/10 text-amber-400 border border-amber-500/30',
                                default => 'bg-slate-500/10 text-slate-400 border border-slate-500/30'
                            };

                            $projectCount = $u->projects->count();
                            $projectText = $projectCount === 0 ? 'Tidak ada proyek' : ($projectCount >= 5 ? 'Semua proyek' : "$projectCount proyek");
                        @endphp
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-4 px-6 flex items-center space-x-3">
                                <div class="w-9 h-9 rounded-xl bg-indigo-600/20 text-indigo-400 font-bold flex items-center justify-center text-xs border border-indigo-500/30 shrink-0">
                                    {{ $initials }}
                                </div>
                                <span class="font-bold text-white">{{ $u->name }}</span>
                            </td>
                            <td class="py-4 px-6 text-slate-400">{{ $u->email }}</td>
                            <td class="py-4 px-6">
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold {{ $roleBadgeClass }}">
                                    {{ $u->role }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-slate-300">{{ $projectText }}</td>
                            <td class="py-4 px-6 text-right space-x-3">
                                <button @click="isEdit = true; user = {{ json_encode($u) }}; showModal = true" class="text-indigo-400 hover:text-indigo-300 font-semibold cursor-pointer">Edit</button>
                                <form action="{{ route('users.destroy', $u->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-300 font-semibold cursor-pointer">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-12 text-slate-500 text-xs">Belum ada pengguna terdaftar.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- MODAL TAMBAH / EDIT PENGGUNA -->
    <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4" style="display: none;">
        <div @click.away="showModal = false" class="w-full max-w-lg p-6 rounded-2xl bg-[#131b2e] border border-slate-800 space-y-4 shadow-2xl mx-4 md:mx-0 max-h-[85vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-sm font-bold text-white" x-text="isEdit ? 'Edit Pengguna' : 'Undang Pengguna Baru'"></h3>
                <button @click="showModal = false" class="text-slate-400 hover:text-white text-lg font-bold cursor-pointer">&times;</button>
            </div>
            
            <form :action="isEdit ? '/users/' + user.id : '/users'" method="POST" class="space-y-4">
                @csrf
                <template x-if="isEdit"><input type="hidden" name="_method" value="PUT"></template>
                
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" x-model="user.name" required class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-indigo-500 text-xs">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 mb-1">Email</label>
                    <input type="email" name="email" x-model="user.email" required class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-indigo-500 text-xs">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 mb-1">Role / Peran</label>
                    <select name="role" x-model="user.role" required class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-indigo-500 text-xs">
                        <option value="Admin">Admin</option>
                        <option value="QA Lead">QA Lead</option>
                        <option value="QA Tester">QA Tester</option>
                        <option value="Developer">Developer</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 mb-1">
                        Password <span x-show="isEdit" class="text-[10px] text-slate-500 font-normal">(Kosongkan jika tidak ingin mengubah)</span>
                    </label>
                    <input type="password" name="password" class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-indigo-500 text-xs" :required="!isEdit">
                </div>
                <div class="flex items-center justify-end space-x-3 pt-2">
                    <button type="button" @click="showModal = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold transition cursor-pointer">Simpan</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>