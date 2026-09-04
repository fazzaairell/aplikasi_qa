<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proyek - QA Management</title>
    <meta name="description" content="Kelola seluruh proyek pengujian kualitas Anda dalam satu platform terpusat.">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        * { font-family: 'Inter', sans-serif; }
        body { background: #080c17; }
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: #080c17; }
        ::-webkit-scrollbar-thumb { background: rgba(99,102,241,.35); border-radius: 99px; }

        @keyframes fadeUp   { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
        @keyframes float    { 0%,100% { transform:translateY(0px); } 50% { transform:translateY(-6px); } }
        @keyframes gradientShift { 0%,100% { background-position:0% 50%; } 50% { background-position:100% 50%; } }
        @keyframes pulse-ring { 0% { transform:scale(.9); opacity:.7; } 70% { transform:scale(1.3); opacity:0; } 100% { transform:scale(1.3); opacity:0; } }

        .fade-up { animation: fadeUp .5s ease both; }
        .delay-1 { animation-delay:.08s; }
        .delay-2 { animation-delay:.16s; }
        .delay-3 { animation-delay:.24s; }
        .delay-4 { animation-delay:.32s; }
        .delay-5 { animation-delay:.40s; }
        .delay-6 { animation-delay:.48s; }

        .stat-card {
            background: linear-gradient(135deg, #0f1629 0%, #111827 100%);
            border: 1px solid rgba(255,255,255,.06);
            border-radius: 1.125rem;
            transition: transform .25s ease, border-color .25s ease, box-shadow .25s ease;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            border-color: rgba(99,102,241,.3);
            box-shadow: 0 20px 40px rgba(0,0,0,.4);
        }

        .project-card {
            background: linear-gradient(145deg, #0e1629 0%, #111827 100%);
            border: 1px solid rgba(255,255,255,.06);
            border-radius: 1.25rem;
            transition: transform .3s cubic-bezier(.34,1.56,.64,1), border-color .3s ease, box-shadow .3s ease;
            position: relative;
            overflow: hidden;
        }
        .project-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse at top right, rgba(99,102,241,.07), transparent 65%);
            pointer-events: none;
            opacity: 0;
            transition: opacity .3s ease;
        }
        .project-card:hover { transform: translateY(-5px) scale(1.01); border-color: rgba(99,102,241,.35); box-shadow: 0 24px 48px rgba(0,0,0,.5), 0 0 0 1px rgba(99,102,241,.1); }
        .project-card:hover::before { opacity: 1; }

        .badge-aktif   { background:rgba(16,185,129,.12); border:1px solid rgba(16,185,129,.25); color:#34d399; }
        .badge-pending { background:rgba(245,158,11,.12);  border:1px solid rgba(245,158,11,.25);  color:#fbbf24; }
        .badge-selesai { background:rgba(99,102,241,.12);  border:1px solid rgba(99,102,241,.25);  color:#818cf8; }

        .gradient-text {
            background: linear-gradient(135deg, #a78bfa 0%, #818cf8 50%, #60a5fa 100%);
            background-size: 200% 200%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: gradientShift 4s ease infinite;
        }

        .search-bar {
            background: rgba(17,24,39,.8);
            border: 1px solid rgba(255,255,255,.07);
            border-radius: .875rem;
            backdrop-filter: blur(10px);
            transition: border-color .2s, box-shadow .2s;
        }
        .search-bar:focus-within { border-color: rgba(99,102,241,.5); box-shadow: 0 0 0 3px rgba(99,102,241,.08); }

        .form-input {
            width: 100%; padding: .625rem 1rem;
            background: rgba(8,12,23,.9);
            border: 1px solid rgba(255,255,255,.08);
            border-radius: .75rem; color: #f1f5f9; font-size: .75rem; outline: none;
            transition: border-color .2s, box-shadow .2s;
        }
        .form-input:focus { border-color: rgba(99,102,241,.6); box-shadow: 0 0 0 3px rgba(99,102,241,.08); }
        .form-input::placeholder { color: #475569; }

        .modal-card {
            background: linear-gradient(145deg, #0e1629 0%, #111827 100%);
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 1.5rem;
        }

        .icon-float { animation: float 3.5s ease-in-out infinite; }

        .progress-bar { background: rgba(255,255,255,.06); border-radius: 99px; overflow: hidden; }
        .progress-fill { height: 100%; border-radius: 99px; transition: width .6s cubic-bezier(.4,0,.2,1); }

        .empty-card { background: linear-gradient(145deg, #0e1629 0%, #111827 100%); border: 2px dashed rgba(99,102,241,.2); border-radius: 1.5rem; }

        .filter-chip { color: #64748b; background: transparent; border: 1px solid transparent; }
        .filter-chip:hover { color: #94a3b8; background: rgba(255,255,255,.05); }
        .filter-chip.active { color: #a78bfa; background: rgba(99,102,241,.12); border-color: rgba(99,102,241,.25); }

        .view-btn { color: #475569; }
        .view-btn:hover { color: #94a3b8; background: rgba(255,255,255,.06); }
        .view-btn.active { color: #a78bfa; background: rgba(99,102,241,.15); }

        #project-grid.list-view { grid-template-columns: 1fr !important; }
    </style>
</head>
<body class="h-full font-sans text-slate-100 flex overflow-hidden" x-data="{
    showAddModal: false,
    showEditModal: false,
    editForm: { id: '', name: '', description: '', status: 'Aktif', test_plan: { scope: '', objective: '', resource: '', schedule: '', risk: '' } },
    sidebarOpen: false,
    collapsed: false
    }">

    <x-sidebar />

    <!-- MAIN CONTENT -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto h-full">

        <!-- TOP BAR -->
        <header class="h-16 border-b px-4 sm:px-8 flex items-center justify-between sticky top-0 z-30"
                style="background:rgba(8,12,23,.88);backdrop-filter:blur(16px);border-color:rgba(255,255,255,.05);">
            <div class="flex items-center gap-4">
                <button @click="$dispatch('toggle-sidebar')"
                        class="md:hidden text-slate-400 hover:text-white p-2 rounded-xl bg-white/5 border border-white/8 cursor-pointer transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <div class="search-bar flex items-center gap-2.5 px-4 py-2 w-36 sm:w-56 md:w-80">
                    <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                    </svg>
                    <input type="text" id="searchInput" oninput="filterData()"
                           placeholder="Cari proyek..."
                           class="bg-transparent flex-1 text-xs text-white placeholder-slate-500 focus:outline-none">
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="hidden sm:flex items-center gap-2">
                    <button onclick="filterByStatus('')"       id="filter-all"     class="filter-chip active px-3 py-1.5 rounded-lg text-[11px] font-semibold transition cursor-pointer">Semua</button>
                    <button onclick="filterByStatus('Aktif')"   id="filter-aktif"   class="filter-chip px-3 py-1.5 rounded-lg text-[11px] font-semibold transition cursor-pointer">Aktif</button>
                    <button onclick="filterByStatus('Pending')" id="filter-pending" class="filter-chip px-3 py-1.5 rounded-lg text-[11px] font-semibold transition cursor-pointer">Pending</button>
                    <button onclick="filterByStatus('Selesai')" id="filter-selesai" class="filter-chip px-3 py-1.5 rounded-lg text-[11px] font-semibold transition cursor-pointer">Selesai</button>
                </div>
                <button @click="showAddModal = true"
                        class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs transition shadow-lg shadow-indigo-600/30 cursor-pointer group">
                    <svg class="w-4 h-4 transition group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16M4 12h16"/>
                    </svg>
                    <span class="hidden sm:inline">Tambah Proyek</span>
                    <span class="sm:hidden">Baru</span>
                </button>
            </div>
        </header>

        <main class="p-6 sm:p-8 space-y-8 max-w-screen-2xl mx-auto w-full">

            <!-- PAGE HEADING -->
            <div class="fade-up flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                <div>
                    <p class="text-[11px] font-bold tracking-[.18em] uppercase text-indigo-400 mb-1.5">Manajemen Proyek</p>
                    <h1 class="text-3xl font-black tracking-tight">
                        <span class="text-3xl font-bold text-white tracking-tight">Daftar Proyek</span>
                    </h1>
                    <p class="text-slate-400 text-sm mt-1.5">Kelola semua proyek pengujian kualitas Anda di sini.</p>
                </div>
                <div class="flex items-center gap-2 bg-white/5 rounded-xl p-1 border border-white/6 self-start sm:self-auto">
                    <button id="view-grid" onclick="setView('grid')" class="view-btn active p-2 rounded-lg transition cursor-pointer" title="Grid View">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    </button>
                    <button id="view-list" onclick="setView('list')" class="view-btn p-2 rounded-lg transition cursor-pointer" title="List View">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    </button>
                </div>
            </div>

            @if(session('success'))
                <div class="fade-up flex items-center gap-3 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-semibold">
                    <div class="w-8 h-8 rounded-full bg-emerald-500/20 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    {{ session('success') }}
                </div>
            @endif

            <!-- PROJECT GRID -->
            <div id="project-grid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                @forelse($projects as $i => $project)
                    @php
                        $statusClass = match($project->status ?? 'Aktif') {
                            'Aktif'   => 'badge-aktif',
                            'Pending' => 'badge-pending',
                            'Selesai' => 'badge-selesai',
                            default   => 'badge-aktif',
                        };
                        $dotColor = match($project->status ?? 'Aktif') {
                            'Aktif'   => 'bg-emerald-400',
                            'Pending' => 'bg-amber-400',
                            'Selesai' => 'bg-indigo-400',
                            default   => 'bg-emerald-400',
                        };
                        $iconGrad = match($project->status ?? 'Aktif') {
                            'Aktif'   => 'from-emerald-600/30 to-teal-600/20',
                            'Pending' => 'from-amber-600/30 to-orange-600/20',
                            'Selesai' => 'from-indigo-600/30 to-purple-600/20',
                            default   => 'from-emerald-600/30 to-teal-600/20',
                        };
                        $delay = 'delay-' . (($i % 6) + 1);
                    @endphp
                    <div class="project-card data-item fade-up {{ $delay }} flex flex-col"
                         data-status="{{ $project->status ?? 'Aktif' }}">

                        <!-- top accent line -->


                        <div class="p-6 flex flex-col flex-1">
                            <!-- Header row -->
                            <div class="flex items-start justify-between gap-3 mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-11 h-11 rounded-xl bg-gradient-to-br {{ $iconGrad }} flex items-center justify-center shrink-0 border border-white/5">
                                        <svg class="w-5 h-5 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                    </div>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold {{ $statusClass }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $dotColor }}"></span>
                                        {{ $project->status ?? 'Aktif' }}
                                    </span>
                                </div>
                                <!-- Actions -->
                                <div class="flex items-center gap-1">
                                    <a href="{{ route('projects.show', $project->id) }}"
                                       class="p-1.5 rounded-lg text-slate-400 hover:text-indigo-400 hover:bg-indigo-500/10 transition" title="Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <button @click="showEditModal = true; editForm = {
                                                id: '{{ $project->id }}',
                                                name: '{{ $project->name }}',
                                                description: '{{ $project->description }}',
                                                status: '{{ $project->status ?? 'Aktif' }}',
                                                test_plan: {{ Js::from($project->test_plan ?? ['scope'=>'','objective'=>'','resource'=>'','schedule'=>'','risk'=>'']) }}
                                            }"
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-amber-400 hover:bg-amber-500/10 transition cursor-pointer" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <form action="{{ route('projects.destroy', $project->id) }}" method="POST"
                                          class="inline" onsubmit="return confirm('Hapus proyek ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-1.5 rounded-lg text-slate-400 hover:text-red-400 hover:bg-red-500/10 transition cursor-pointer" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Name & description -->
                            <div class="flex-1 space-y-2 mb-5">
                                <h2 class="text-base font-bold text-white leading-snug line-clamp-2">{{ $project->name }}</h2>
                                <p class="text-xs text-slate-500 leading-relaxed line-clamp-2">
                                    {{ $project->description ?: 'Tidak ada deskripsi untuk proyek ini.' }}
                                </p>
                            </div>

                            <!-- Test plan chips -->

                            <!-- Footer -->
                            <div class="pt-4 border-t border-white/5 flex items-center justify-between">
                                <a href="{{ route('requirements.index', ['project_id' => $project->id]) }}"
                                   class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-indigo-400 hover:text-indigo-300 transition group">
                                    Kelola Requirements
                                    <svg class="w-3.5 h-3.5 transition group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                                <div class="flex items-center gap-1.5 text-[10px] text-slate-600">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $project->created_at->format('d M Y') }}
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full empty-card py-20 flex flex-col items-center justify-center text-center fade-up">
                        <div class="w-20 h-20 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center mb-6 icon-float">
                            <svg class="w-10 h-10 text-indigo-400/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-300 mb-2">Belum Ada Proyek</h3>
                        <p class="text-slate-500 text-sm max-w-xs leading-relaxed mb-6">Mulailah dengan membuat proyek pertama Anda untuk mengelola siklus pengujian secara terpusat.</p>
                        <button @click="showAddModal = true"
                                class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-sm transition shadow-lg shadow-indigo-600/30 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16M4 12h16"/>
                            </svg>
                            Buat Proyek Pertama
                        </button>
                    </div>
                @endforelse
            </div>

            <!-- No results -->
            <div id="no-results" class="hidden empty-card py-16 flex flex-col items-center justify-center text-center">
                <svg class="w-12 h-12 text-slate-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                </svg>
                <p class="text-slate-500 text-sm font-medium">Tidak ada proyek yang cocok.</p>
            </div>
        </main>
    </div>

    <!-- ═══ MODAL TAMBAH ═══ -->
    <div x-show="showAddModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/75 backdrop-blur-sm p-4"
         style="display:none;">
        <div @click.away="showAddModal = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="modal-card w-full max-w-lg shadow-2xl mx-4 md:mx-0 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-5 border-b border-white/7">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-indigo-500/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16M4 12h16"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-white">Tambah Proyek Baru</h3>
                        <p class="text-[11px] text-slate-500">Isi detail proyek pengujian</p>
                    </div>
                </div>
                <button @click="showAddModal = false"
                        class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-white hover:bg-white/8 rounded-lg transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form action="{{ route('projects.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 mb-1.5">Nama Proyek <span class="text-red-400">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: E-Commerce Platform v2" class="form-input">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 mb-1.5">Status</label>
                    <select name="status" class="form-input">
                        <option value="Aktif">🟢 Aktif</option>
                        <option value="Pending">🟡 Pending</option>
                        <option value="Selesai">🔵 Selesai</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 mb-1.5">Deskripsi <span class="text-red-400">*</span></label>
                    <textarea name="description" rows="3" required placeholder="Deskripsi singkat mengenai proyek..." class="form-input resize-none"></textarea>
                </div>
                <div class="rounded-xl border border-indigo-500/15 bg-indigo-500/5 p-4 space-y-3">
                    <div class="flex items-center gap-2 mb-1">
                        <div class="w-1 h-4 rounded-full bg-indigo-500"></div>
                        <h4 class="text-xs font-bold text-indigo-400">Detail Test Plan</h4>
                        <span class="text-[10px] text-slate-500 ml-auto">Opsional</span>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 mb-1.5">Scope</label>
                        <textarea name="test_plan[scope]" rows="2" placeholder="Ruang lingkup pengujian..." class="form-input resize-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 mb-1.5">Objective</label>
                        <textarea name="test_plan[objective]" rows="2" placeholder="Tujuan pengujian..." class="form-input resize-none"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 mb-1.5">Resource</label>
                            <input type="text" name="test_plan[resource]" placeholder="3 QA Engineer" class="form-input">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 mb-1.5">Schedule</label>
                            <input type="text" name="test_plan[schedule]" placeholder="12–20 Agt 2026" class="form-input">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 mb-1.5">Risk</label>
                        <textarea name="test_plan[risk]" rows="2" placeholder="Potensi risiko dan mitigasi..." class="form-input resize-none"></textarea>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="showAddModal = false"
                            class="px-4 py-2.5 rounded-xl bg-white/6 hover:bg-white/10 text-slate-300 text-xs font-semibold transition cursor-pointer">Batal</button>
                    <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition shadow-lg shadow-indigo-600/30 cursor-pointer">Buat Proyek</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ═══ MODAL EDIT ═══ -->
    <div x-show="showEditModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/75 backdrop-blur-sm p-4"
         style="display:none;">
        <div @click.away="showEditModal = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="modal-card w-full max-w-lg shadow-2xl mx-4 md:mx-0 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-5 border-b border-white/7">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-amber-500/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-white">Edit Proyek</h3>
                        <p class="text-[11px] text-slate-500">Perbarui informasi proyek</p>
                    </div>
                </div>
                <button @click="showEditModal = false"
                        class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-white hover:bg-white/8 rounded-lg transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form :action="'/projects/' + editForm.id" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 mb-1.5">Nama Proyek <span class="text-red-400">*</span></label>
                    <input type="text" name="name" x-model="editForm.name" required class="form-input">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 mb-1.5">Status</label>
                    <select name="status" x-model="editForm.status" class="form-input">
                        <option value="Aktif">🟢 Aktif</option>
                        <option value="Pending">🟡 Pending</option>
                        <option value="Selesai">🔵 Selesai</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 mb-1.5">Deskripsi <span class="text-red-400">*</span></label>
                    <textarea name="description" rows="3" x-model="editForm.description" required class="form-input resize-none"></textarea>
                </div>
                <div class="rounded-xl border border-indigo-500/15 bg-indigo-500/5 p-4 space-y-3">
                    <div class="flex items-center gap-2 mb-1">
                        <div class="w-1 h-4 rounded-full bg-indigo-500"></div>
                        <h4 class="text-xs font-bold text-indigo-400">Detail Test Plan</h4>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 mb-1.5">Scope</label>
                        <textarea name="test_plan[scope]" rows="2" x-model="editForm.test_plan.scope" class="form-input resize-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 mb-1.5">Objective</label>
                        <textarea name="test_plan[objective]" rows="2" x-model="editForm.test_plan.objective" class="form-input resize-none"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 mb-1.5">Resource</label>
                            <input type="text" name="test_plan[resource]" x-model="editForm.test_plan.resource" class="form-input">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 mb-1.5">Schedule</label>
                            <input type="text" name="test_plan[schedule]" x-model="editForm.test_plan.schedule" class="form-input">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 mb-1.5">Risk</label>
                        <textarea name="test_plan[risk]" rows="2" x-model="editForm.test_plan.risk" class="form-input resize-none"></textarea>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="showEditModal = false"
                            class="px-4 py-2.5 rounded-xl bg-white/6 hover:bg-white/10 text-slate-300 text-xs font-semibold transition cursor-pointer">Batal</button>
                    <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-500 text-white text-xs font-bold transition shadow-lg shadow-amber-600/25 cursor-pointer">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <x-profile-modal />
    <script>
        let activeStatus = '';

        function filterData() {
            const search = (document.getElementById('searchInput')?.value || '').toLowerCase().trim();
            applyFilters(search, activeStatus);
        }

        function filterByStatus(status) {
            activeStatus = status;
            document.querySelectorAll('.filter-chip').forEach(c => c.classList.remove('active'));
            const map = { '': 'filter-all', 'Aktif': 'filter-aktif', 'Pending': 'filter-pending', 'Selesai': 'filter-selesai' };
            document.getElementById(map[status])?.classList.add('active');
            filterData();
        }

        function applyFilters(search, status) {
            const items = document.querySelectorAll('.data-item');
            let visible = 0;
            items.forEach(item => {
                const textMatch   = search === '' || item.innerText.toLowerCase().includes(search);
                const statusMatch = status === '' || (item.dataset.status || '') === status;
                const show = textMatch && statusMatch;
                item.style.display = show ? '' : 'none';
                if (show) visible++;
            });
            const noResults = document.getElementById('no-results');
            if (noResults) noResults.classList.toggle('hidden', visible > 0 || items.length === 0);
        }

        function setView(view) {
            const grid = document.getElementById('project-grid');
            document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active'));
            document.getElementById('view-' + view)?.classList.add('active');
            grid.classList.toggle('list-view', view === 'list');
        }
    </script>
</body>
</html>