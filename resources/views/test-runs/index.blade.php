<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Runs - QA Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');</script>
    <style>* { font-family: 'Inter', sans-serif; } body { background: #0c0f1a; } ::-webkit-scrollbar{width:5px;height:5px} ::-webkit-scrollbar-track{background:#0c0f1a} ::-webkit-scrollbar-thumb{background:rgba(99,102,241,.3);border-radius:99px}</style>
</head>
<body class="h-full font-sans text-slate-100 flex overflow-hidden" x-data='{ 
    sidebarOpen: false, 
    collapsed: false,
    showCreateModal: false,
    showEditModal: false,
    showBugModal: false,
    activeTestResultId: null,
    editForm: { id: null, title: "", status: "Active" },
    bugForm: { bug_title: "", bug_description: "", expected_result: "", assigned_to: "", due_date: "", attachment: null, image_url: null },
    projectsData: @json($projects),
    form: { project_id: "{{ $selectedProjectId ?? "" }}", test_suite_id: "", title: "" },
    loading: false,
    expandedRuns: {
        @foreach($testRuns as $index => $run)
            {{ $run->id }}: {{ $index === 0 ? "true" : "false" }},
        @endforeach
    },
    toggleRun(id) {
        this.expandedRuns[id] = !this.expandedRuns[id];
    },
    submitTestRun() {
        this.loading = true;
        axios.post("{{ route("test-runs.store") }}", this.form)
            .then(response => {
                alert(response.data.message);
                window.location.href = "?project_id=" + this.form.project_id;
            })
            .catch(error => {
                alert("Gagal memulai test run.");
                console.error(error);
            })
            .finally(() => { this.loading = false; });
    },
    openEditModal(run) {
        this.editForm.id = run.id;
        this.editForm.title = run.title;
        this.editForm.status = run.status;
        this.showEditModal = true;
    },
    updateTestRun() {
        axios.put(`/test-runs/${this.editForm.id}`, this.editForm)
            .then(response => {
                alert(response.data.message);
                window.location.reload();
            })
            .catch(error => {
                alert("Gagal memperbarui test run.");
                console.error(error);
            });
    },
    deleteTestRun(id) {
        if (confirm("Apakah Anda yakin ingin menghapus Test Run ini? Semua data hasil tes di dalamnya akan ikut terhapus.")) {
            axios.delete(`/test-runs/${id}`)
                .then(response => {
                    alert(response.data.message);
                    window.location.reload();
                })
                .catch(error => {
                    alert("Gagal menghapus test run.");
                    console.error(error);
                });
        }
    },
    changeStatus(testResultId, status) {
        if (status === "Failed") {
            this.activeTestResultId = testResultId;
            this.bugForm = { bug_title: "", bug_description: "", expected_result: "", assigned_to: "", due_date: "", attachment: null, image_url: null };
            this.showBugModal = true;
            return;
        }
        this.sendUpdateResult(testResultId, { status: status });
    },
    submitBugResult() {
        let formData = new FormData();
        formData.append("status", "Failed");
        formData.append("bug_title", this.bugForm.bug_title);
        formData.append("bug_description", this.bugForm.bug_description);
        formData.append("expected_result", this.bugForm.expected_result);
        formData.append("assigned_to", this.bugForm.assigned_to);
        formData.append("due_date", this.bugForm.due_date);

        if (this.bugForm.attachment) {
            formData.append("attachment", this.bugForm.attachment);
        }
        
        formData.append("_method", "PATCH");

        axios.post(`/test-results/${this.activeTestResultId}/update`, formData, {
    headers: { 
        "Content-Type": "multipart/form-data",
        "Accept": "application/json"
    }
})
        .then(response => { window.location.reload(); })
    .catch(error => { 
        let errorMsg;
        if (error.response?.status === 413) {
            errorMsg = "Ukuran gambar terlalu besar untuk diupload ke server. Silakan gunakan gambar yang lebih kecil (maksimal 5MB) atau hubungi admin untuk menaikkan batas upload server.";
        } else if (error.response?.status === 422) {
            const errors = error.response.data.errors;
            errorMsg = "Validasi gagal:\n" + Object.values(errors).flat().join("\n");
        } else {
            errorMsg = error.response?.data?.message || "Gagal memperbarui status tes.";
        }
        alert(errorMsg); 
        console.error(error);
    });
    },
    sendUpdateResult(testResultId, payload) {
        axios.patch(`/test-results/${testResultId}/update`, payload)
            .then(response => { window.location.reload(); })
            .catch(error => { alert("Gagal memperbarui status tes."); });
    }
}'>

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
                    <input type="text" placeholder="Cari test run..." class="w-full px-4 py-2 bg-[#131b2e] border border-slate-800 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500">
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <button @click="showCreateModal = true" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs transition shadow-lg shadow-indigo-600/30 cursor-pointer inline-flex items-center">
                    Mulai Test Run
                </button>
            </div>
        </header>

        <main class="p-8 space-y-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="text-xs text-indigo-400 font-semibold tracking-wider mb-1">EKSEKUSI</div>
                    <h1 class="text-2xl font-bold text-white tracking-tight">Test Runs</h1>
                </div>

                <!-- FILTER PROYEK -->
                <form method="GET" action="{{ route('test-runs.index') }}" class="flex items-center space-x-2">
                    <select name="project_id" onchange="this.form.submit()" class="px-3 py-2 bg-[#131b2e] border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-indigo-500">
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ $selectedProjectId == $project->id ? 'selected' : '' }}>
                                {{ $project->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            <!-- LIST TEST RUNS -->
            <div class="space-y-6">
                @forelse($testRuns as $run)
                @php
                        $total = $run->testResults->count();
                        $passed = $run->testResults->where('status', 'Passed')->count();
                        $failed = $run->testResults->where('status', 'Failed')->count();
                        $blocked = $run->testResults->where('status', 'Blocked')->count();
                        $untested = $run->testResults->where('status', 'Untested')->count();

                        $passedPct = $total > 0 ? ($passed / $total) * 100 : 0;
                        $failedPct = $total > 0 ? ($failed / $total) * 100 : 0;
                        $blockedPct = $total > 0 ? ($blocked / $total) * 100 : 0;
                        $untestedPct = $total > 0 ? ($untested / $total) * 100 : 0;
                    @endphp

                    <div class="bg-[#131b2e] border border-slate-800/80 rounded-2xl shadow-xl overflow-hidden">
                        <!-- CARD HEADER -->
                        <div class="p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="flex items-start space-x-3">
                                <div class="w-10 h-10 rounded-xl bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                </div>
                                <div class="space-y-1">
                                    <div class="flex items-center space-x-2 flex-wrap">
                                        <h2 class="text-sm font-bold text-white">{{ $run->title }}</h2>
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold 
                                            {{ $run->status === 'Active' ? 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20' : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' }}">
                                            {{ $run->status }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-400">{{ $run->project->name ?? '-' }} · {{ $run->created_at->format('Y-m-d') }}</p>
                                </div>
                            </div>

                            <!-- STATS & ACTIONS (DETAIL, EDIT, DELETE) -->
                            <div class="flex items-center justify-between md:justify-end space-x-4">
                                <div class="flex items-center space-x-3 text-center pr-2 border-r border-slate-700/60">
                                    <div>
                                        <div class="text-xs font-bold text-emerald-400">{{ $passed }}</div>
                                        <div class="text-[10px] text-slate-500 uppercase tracking-wider">Passed</div>
                                    </div>
                                    <div>
                                        <div class="text-xs font-bold text-rose-400">{{ $failed }}</div>
                                        <div class="text-[10px] text-slate-500 uppercase tracking-wider">Failed</div>
                                    </div>
                                    <div>
                                        <div class="text-xs font-bold text-amber-400">{{ $blocked }}</div>
                                        <div class="text-[10px] text-slate-500 uppercase tracking-wider">Blocked</div>
                                    </div>
                                    <div>
                                        <div class="text-xs font-bold text-slate-400">{{ $untested }}</div>
                                        <div class="text-[10px] text-slate-500 uppercase tracking-wider">Untested</div>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-1.5">
                                    <!-- TOMBOL EDIT -->
                                    <button @click="openEditModal({{ json_encode($run) }})" class="p-2 rounded-lg bg-[#0b0f19] border border-slate-700/80 hover:bg-slate-800 text-slate-300 hover:text-indigo-400 transition cursor-pointer" title="Edit Test Run">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <!-- TOMBOL HAPUS -->
                                    <button @click="deleteTestRun({{ $run->id }})" class="p-2 rounded-lg bg-[#0b0f19] border border-slate-700/80 hover:bg-rose-900/30 text-slate-300 hover:text-rose-400 transition cursor-pointer" title="Hapus Test Run">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                    <!-- TOMBOL DETAIL / TOGGLE -->
                                    <button @click="toggleRun({{ $run->id }})" class="px-3.5 py-1.5 rounded-lg bg-[#0b0f19] border border-slate-700/80 hover:bg-slate-800 text-xs font-semibold text-slate-300 transition cursor-pointer">
                                        <span x-text="expandedRuns[{{ $run->id }}] ? 'Tutup' : 'Detail'">Detail</span>
                                    </button>
                                    <!-- TOMBOL LIHAT RINGKASAN — hanya muncul saat semua sudah dieksekusi -->
                                    @if($total > 0 && $untested === 0)
                                    <a href="{{ route('test-runs.summary', $run->id) }}"
                                       class="px-3.5 py-1.5 rounded-lg text-xs font-semibold text-white transition cursor-pointer flex items-center gap-1"
                                       style="background:linear-gradient(135deg,#4f46e5,#6366f1);"
                                       title="Lihat Test Summary Report">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        Ringkasan
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- MULTI-COLOR PROGRESS BAR -->
                        <div class="w-full bg-slate-900 h-1.5 flex">
                            <div
                                class="progress-bar bg-emerald-500 h-full transition-all duration-500"
                                data-width="{{ $passedPct }}"
                            ></div>

                            <div
                                class="progress-bar bg-rose-500 h-full transition-all duration-500"
                                data-width="{{ $failedPct }}"
                            ></div>

                            <div
                                class="progress-bar bg-amber-500 h-full transition-all duration-500"
                                data-width="{{ $blockedPct }}"
                            ></div>

                            <div
                                class="progress-bar bg-slate-700 h-full transition-all duration-500"
                                data-width="{{ $untestedPct }}"
                            ></div>
                        </div>

                        <!-- EXPANDED TEST CASES SECTION -->
                        <div x-show="expandedRuns[{{ $run->id }}]" class="p-5 bg-[#0b0f19]/60 border-t border-slate-800/80 space-y-3" style="display: none;">
                            <div class="text-[11px] font-bold text-indigo-400 tracking-wider mb-2">EKSEKUSI TEST CASES</div>
                            
                            @forelse($run->testResults as $result)
                                <div class="bg-[#131b2e] border border-slate-800/80 rounded-xl p-3.5 flex flex-col md:flex-row md:items-center justify-between gap-3">
                                    <div class="text-xs font-medium text-white">
                                        {{ $result->testCase->title ?? 'Test Case tidak ditemukan' }}
                                    </div>
                                    <div class="flex items-center space-x-1.5 shrink-0">
                                        <button @click="changeStatus({{ $result->id }}, 'Passed')" class="px-3 py-1 rounded-lg text-[11px] font-semibold transition cursor-pointer border {{ $result->status === 'Passed' ? 'bg-emerald-600 text-white border-emerald-500 shadow-md shadow-emerald-600/30' : 'bg-[#0b0f19] text-slate-400 border-slate-800 hover:border-slate-700 hover:text-slate-200' }}">
                                            Passed
                                        </button>
                                        <button @click="changeStatus({{ $result->id }}, 'Failed')" class="px-3 py-1 rounded-lg text-[11px] font-semibold transition cursor-pointer border {{ $result->status === 'Failed' ? 'bg-rose-600 text-white border-rose-500 shadow-md shadow-rose-600/30' : 'bg-[#0b0f19] text-slate-400 border-slate-800 hover:border-slate-700 hover:text-slate-200' }}">
                                            Failed
                                        </button>
                                        <button @click="changeStatus({{ $result->id }}, 'Blocked')" class="px-3 py-1 rounded-lg text-[11px] font-semibold transition cursor-pointer border {{ $result->status === 'Blocked' ? 'bg-amber-600 text-white border-amber-500 shadow-md shadow-amber-600/30' : 'bg-[#0b0f19] text-slate-400 border-slate-800 hover:border-slate-700 hover:text-slate-200' }}">
                                            Blocked
                                        </button>
                                        <button @click="changeStatus({{ $result->id }}, 'Untested')" class="px-3 py-1 rounded-lg text-[11px] font-semibold transition cursor-pointer border {{ $result->status === 'Untested' ? 'bg-slate-700 text-white border-slate-600' : 'bg-[#0b0f19] text-slate-400 border-slate-800 hover:border-slate-700 hover:text-slate-200' }}">
                                            Untested
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-6 text-slate-500 text-xs">
                                    Belum ada Test Case yang dimuat di Test Run ini.
                                </div>
                            @endforelse
                        </div>
                    </div>
                @empty
                    <div class="text-center py-16 text-slate-500 text-xs bg-[#131b2e] border border-slate-800 rounded-2xl">
                        Belum ada data Test Run untuk proyek ini.
                    </div>
                @endforelse
            </div>
        </main>
    </div>

    <!-- MODAL MULAI TEST RUN -->
    <div x-show="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm" style="display: none;">
        <div @click.away="showCreateModal = false" class="bg-[#131b2e] border border-slate-800 rounded-2xl w-full max-w-md p-6 shadow-2xl space-y-4 mx-4 md:mx-0 max-h-[85vh] overflow-y-auto">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-white">Mulai Test Run Baru</h3>
                <button @click="showCreateModal = false" class="text-slate-400 hover:text-white cursor-pointer">&times;</button>
            </div>
            <form @submit.prevent="submitTestRun" class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">Pilih Proyek</label>
                    <select x-model="form.project_id" @change="form.test_suite_id = ''" required class="w-full px-3 py-2 bg-[#0b0f19] border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-indigo-500">
                        <option value="">-- Pilih Proyek --</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">Pilih Test Suite (Opsional)</label>
                    <select x-model="form.test_suite_id" class="w-full px-3 py-2 bg-[#0b0f19] border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-indigo-500">
                        <option value="">Semua Test Suite</option>
                        <template x-for="suite in (projectsData.find(p => p.id == form.project_id)?.test_suites || [])" :key="suite.id">
                            <option :value="suite.id" x-text="suite.name"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">Judul Test Run</label>
                    <input type="text" x-model="form.title" required placeholder="Contoh: Sprint 3 Regression Test" class="w-full px-3 py-2 bg-[#0b0f19] border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-indigo-500">
                </div>
                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="showCreateModal = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs text-slate-300 cursor-pointer">Batal</button>
                    <button type="submit" :disabled="loading" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-xs text-white font-semibold cursor-pointer">
                        <span x-show="!loading">Mulai Test Run</span>
                        <span x-show="loading">Memproses...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT TEST RUN -->
    <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm" style="display: none;">
        <div @click.away="showEditModal = false" class="bg-[#131b2e] border border-slate-800 rounded-2xl w-full max-w-md p-6 shadow-2xl space-y-4 mx-4 md:mx-0 max-h-[85vh] overflow-y-auto">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-white">Edit Test Run</h3>
                <button @click="showEditModal = false" class="text-slate-400 hover:text-white cursor-pointer">&times;</button>
            </div>
            <form @submit.prevent="updateTestRun" class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">Judul Test Rundfgdfs</label>
                    <input type="text" x-model="editForm.title" required placeholder="Contoh: Sprint 3 Regression Test" class="w-full px-3 py-2 bg-[#0b0f19] border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">Status</label>
                    <select x-model="editForm.status" required class="w-full px-3 py-2 bg-[#0b0f19] border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-indigo-500">
                        <option value="Active">Active</option>
                        <option value="Completed">Completed</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="showEditModal = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs text-slate-300 cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-xs text-white font-semibold cursor-pointer">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL LAPORAN BUG KETIKA FAILED -->
    <div x-show="showBugModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm" style="display: none;">
        <div @click.away="showBugModal = false" class="bg-[#131b2e] border border-slate-700/80 rounded-2xl w-full max-w-lg p-6 shadow-2xl space-y-4 mx-4 md:mx-0 max-h-[90vh] overflow-y-auto">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-white">Laporan Bug</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Isi detail bug yang ditemukan saat pengujian</p>
                </div>
                <button @click="showBugModal = false" class="text-slate-400 hover:text-white transition cursor-pointer p-1 rounded-lg hover:bg-white/5">&times;</button>
            </div>

            <!-- Divider -->
            <div class="border-t border-slate-800/80"></div>

            <div class="space-y-4" x-data="{ previewUrl: null }">
                <!-- Judul Bug -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Judul Bug <span class="text-rose-400">*</span></label>
                    <input type="text" x-model="bugForm.bug_title" required
                           placeholder="Contoh: Button submit tidak merespons saat diklik"
                           class="w-full px-3 py-2.5 bg-[#0b0f19] border border-slate-700 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition">
                </div>

                <!-- Deskripsi -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Deskripsi Bug <span class="text-rose-400">*</span></label>
                    <textarea x-model="bugForm.bug_description" required rows="3"
                              placeholder="Jelaskan langkah-langkah yang menyebabkan bug terjadi..."
                              class="w-full px-3 py-2.5 bg-[#0b0f19] border border-slate-700 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition resize-none"></textarea>
                </div>

                <!-- Expected Result -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Expected Result</label>
                    <textarea x-model="bugForm.expected_result" rows="2"
                              placeholder="Apa yang seharusnya terjadi setelah aksi tersebut?"
                              class="w-full px-3 py-2.5 bg-[#0b0f19] border border-slate-700 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition resize-none"></textarea>
                </div>

                <!-- Row: Assign + Due Date -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Assign ke Developer <span class="text-rose-400">*</span></label>
                        <select x-model="bugForm.assigned_to" required
                                class="w-full px-3 py-2.5 bg-[#0b0f19] border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-indigo-500 transition">
                            <option value="">-- Pilih Developer --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Due Date <span class="text-rose-400">*</span></label>
                        <input type="date" x-model="bugForm.due_date" required
                               class="w-full px-3 py-2.5 bg-[#0b0f19] border border-slate-700 rounded-xl text-xs text-slate-300 focus:outline-none focus:border-indigo-500 transition">
                    </div>
                </div>

                <!-- Upload Screenshot/Attachment -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Screenshot / Lampiran</label>
                    <div class="relative">
                        <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-slate-700 rounded-xl cursor-pointer hover:border-indigo-500/50 hover:bg-indigo-500/5 transition"
                               x-show="!previewUrl">
                            <div class="flex flex-col items-center gap-2 text-slate-500">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span class="text-[11px] font-medium">Klik untuk upload gambar</span>
                                <span class="text-[10px]">JPG, PNG, GIF • Maks. 5MB</span>
                            </div>
                            <input type="file" accept="image/*" class="hidden"
                                   @change="
                                       const file = $event.target.files[0];
                                       if (file) {
                                           bugForm.attachment = file;
                                           previewUrl = URL.createObjectURL(file);
                                       }
                                   ">
                        </label>

                        <!-- Preview Gambar -->
                        <div x-show="previewUrl" class="relative rounded-xl overflow-hidden border border-slate-700">
                            <img :src="previewUrl" alt="Preview" class="w-full h-40 object-cover">
                            <button type="button"
                                    @click="previewUrl = null; bugForm.attachment = null;"
                                    class="absolute top-2 right-2 w-7 h-7 rounded-full bg-rose-500/90 hover:bg-rose-500 text-white flex items-center justify-center text-xs transition cursor-pointer">
                                &times;
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-2 pt-2 border-t border-slate-800/80">
                    <button type="button" @click="showBugModal = false"
                            class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs text-slate-300 cursor-pointer transition">
                        Batal
                    </button>
                    <button type="button"
                            @click="submitBugResult()"
                            :disabled="!bugForm.bug_title || !bugForm.bug_description || !bugForm.assigned_to || !bugForm.due_date"
                            class="px-5 py-2 rounded-xl text-xs text-white font-semibold transition cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed"
                            style="background: linear-gradient(135deg,#dc2626,#b91c1c); box-shadow:0 4px 12px rgba(220,38,38,0.3);">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            Laporkan Bug
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

<script>
document.querySelectorAll('.progress-bar').forEach(function (bar) {
    bar.style.width = bar.dataset.width + '%';
});
</script>
<x-profile-modal />
</body>
</html>