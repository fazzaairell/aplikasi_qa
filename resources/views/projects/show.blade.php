<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $project->name }} - Detail Proyek - QA Management</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Alpine.js untuk interaksi modal/form edit -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>* { font-family: 'Inter', sans-serif; } body { background: #0c0f1a; } ::-webkit-scrollbar{width:5px;height:5px} ::-webkit-scrollbar-track{background:#0c0f1a} ::-webkit-scrollbar-thumb{background:rgba(99,102,241,.3);border-radius:99px}</style>
</head>
<body class="h-full font-sans text-slate-100 flex" x-data="{ showEditForm: false }">

@if(auth()->user()->role !== 'QA Tester')
    <x-sidebar />
@endif

    <!-- MAIN CONTENT AREA -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto bg-[#0b0f19]">
        
        <!-- TOPBAR -->
        <header class="h-16 border-b px-4 sm:px-8 flex items-center justify-between sticky top-0 z-10" style="background:rgba(12,15,26,.85);backdrop-filter:blur(12px);border-color:rgba(255,255,255,.06);">
            <a href="{{ route('projects.index') }}" class="text-xs text-indigo-400 hover:text-indigo-300 font-semibold flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                <span>Kembali ke Daftar Proyek</span>
            </a>
            <span class="text-xs text-slate-400 font-medium">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
        </header>

        <!-- PAGE CONTENT -->
        <main class="p-8 space-y-8">
            
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-semibold">
                    {{ session('success') }}
                </div>
            @endif

            <!-- HEADER DETAIL & TOMBOL AKSI (EDIT / HAPUS) -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-6 rounded-2xl bg-[#131b2e] border border-slate-800/80 shadow-xl">
                <div class="space-y-1">
                    <div class="flex items-center space-x-3">
                        <h1 class="text-2xl font-bold text-white tracking-tight">{{ $project->name }}</h1>
                        <span class="px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-400 text-[10px] font-bold border border-emerald-500/20">
                            {{ $project->status ?? 'Aktif' }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-400">{{ $project->description ?: 'Tidak ada deskripsi.' }}</p>
                </div>

                <div class="flex items-center space-x-3">
                    <!-- Tombol Edit -->
                    <button @click="showEditForm = !showEditForm" class="px-4 py-2 rounded-xl bg-amber-600/20 hover:bg-amber-600/30 text-amber-400 border border-amber-500/30 font-semibold text-xs transition flex items-center space-x-1.5">
                        <span>Edit Proyek</span>
                    </button>

                    <!-- Tombol Hapus -->
                    <form action="{{ route('projects.destroy', $project->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus proyek ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 rounded-xl bg-red-600/20 hover:bg-red-600/30 text-red-400 border border-red-500/30 font-semibold text-xs transition">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>

            <!-- FORM EDIT PROYEK (Toggle dengan Alpine.js) -->
            <div x-show="showEditForm" x-transition class="p-6 rounded-2xl bg-[#131b2e] border border-slate-800/80 shadow-xl">
                <h2 class="text-base font-bold text-white mb-4">Edit Proyek</h2>
                <form action="{{ route('projects.update', $project->id) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 mb-1">Nama Proyek</label>
                        <input type="text" name="name" value="{{ $project->name }}" required class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-indigo-500 text-xs">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 mb-1">Status</label>
                        <select name="status" class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-indigo-500 text-xs">
                            <option value="Aktif" {{ $project->status == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="Selesai" {{ $project->status == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="Pending" {{ $project->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 mb-1">Deskripsi</label>
                        <textarea name="description" rows="3" class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-indigo-500 text-xs">{{ $project->description }}</textarea>
                    </div>
                    <div class="flex items-center space-x-3">
                        <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs transition">Simpan Perubahan</button>
                        <button type="button" @click="showEditForm = false" class="px-5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs transition">Batal</button>
                    </div>
                </form>
            </div>

            <!-- INFORMASI TAMBAHAN / TEST SUITES TERKAIT -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="p-6 rounded-2xl bg-[#131b2e] border border-slate-800/80 space-y-4">
                    <h3 class="text-sm font-bold text-white">Statistik & Test Suites</h3>
                    <p class="text-xs text-slate-400">Total Test Cases: <span class="font-bold text-white">{{ isset($project->testSuites) ? $project->testSuites->flatMap->testCases->count() : 0 }}</span></p>
                    <p class="text-xs text-slate-400">Dibuat pada: <span class="font-bold text-white">{{ $project->created_at->format('d F Y, H:i') }}</span></p>
                </div>
            </div>

        </main>
    </div>

</body>
</html>