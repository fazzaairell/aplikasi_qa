<!DOCTYPE html>
<html lang="id" class="h-full bg-[#0b0f19]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Runs - QA Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body class="h-full font-sans text-slate-100 flex overflow-hidden" x-data='{ 
    sidebarOpen: false, 
    collapsed: false,
    showCreateModal: false,
    showEditModal: false,
    showBugModal: false,
    activeTestResultId: null,
    editForm: { id: null, title: "", status: "Active" },
    bugForm: { bug_title: "", bug_description: "", assigned_to: "", due_date: "", attachment: null },
    form: { project_id: "{{ $selectedProjectId ?? "" }}", title: "" },
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
                window.location.reload();
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
            this.bugForm = { bug_title: "", bug_description: "", assigned_to: "", due_date: "", attachment: null };
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
        formData.append("assigned_to", this.bugForm.assigned_to);
        formData.append("due_date", this.bugForm.due_date);   // <-- baris baru
        
        if (this.bugForm.attachment) {
            formData.append("attachment", this.bugForm.attachment);
        }
        
        formData.append("_method", "PATCH");

        axios.post(`/test-results/${this.activeTestResultId}/update`, formData, {
            headers: { "Content-Type": "multipart/form-data" }
        })
        .then(response => { window.location.reload(); })
        .catch(error => { 
            let errorMsg = error.response?.data?.message || "Gagal memperbarui status tes.";
            alert(errorMsg); 
            console.error(error);
        });
    },
    sendUpdateResult(testResultId, payload) {
        axios.patch(`/test-results/${testResultId}/update`, payload)
            .then(response => { window.location.reload(); })
            .catch(error => { alert("Gagal memperbarui status tes."); });
    }
}'

@if(auth()->user()->role !== 'QA Tester')
    <x-sidebar />
@endif
    <!-- MAIN CONTENT -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto bg-[#0b0f19] h-full">
        
        <!-- TOPBAR -->
        <header class="h-20 border-b border-slate-800/80 px-8 flex items-center justify-between sticky top-0 bg-[#0b0f19]/80 backdrop-blur-md z-30">
            <div class="flex items-center space-x-4">
                <button @click="sidebarOpen = !sidebarOpen" class="md:hidden text-slate-400 hover:text-white p-2 rounded-lg bg-[#131b2e] border border-slate-800 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <div class="w-48 md:w-96">
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
                                </div>
                            </div>
                        </div>

                        <!-- MULTI-COLOR PROGRESS BAR -->
   
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
        <div @click.away="showCreateModal = false" class="bg-[#131b2e] border border-slate-800 rounded-2xl w-full max-w-md p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-white">Mulai Test Run Baru</h3>
                <button @click="showCreateModal = false" class="text-slate-400 hover:text-white cursor-pointer">&times;</button>
            </div>
            <form @submit.prevent="submitTestRun" class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">Pilih Proyek</label>
                    <select x-model="form.project_id" required class="w-full px-3 py-2 bg-[#0b0f19] border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-indigo-500">
                        <option value="">-- Pilih Proyek --</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
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
<!-- MODAL LAPORAN BUG KETIKA FAILED -->
<div x-show="showBugModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm" style="display: none;">
    <div @click.away="showBugModal = false" class="bg-[#131b2e] border border-slate-800 rounded-2xl w-full max-w-lg p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-bold text-white flex items-center space-x-2">
                <span class="w-2.5 h-2.5 rounded-full bg-rose-500 inline-block"></span>
                <span>Laporkan Bug (Test Case Gagal)</span>
            </h3>
            <button @click="showBugModal = false" class="text-slate-400 hover:text-white cursor-pointer">&times;</button>
        </div>
        <form @submit.prevent="submitBugResult" class="space-y-4">
            <div>
                <label class="block text-xs font-medium text-slate-300 mb-1">Judul Bug / Kendala</label>
                <input type="text" x-model="bugForm.bug_title" required placeholder="Contoh: Error 500 saat klik tombol submit" class="w-full px-3 py-2 bg-[#0b0f19] border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-300 mb-1">Deskripsi & Langkah Reproduksi</label>
                <textarea x-model="bugForm.bug_description" rows="3" required placeholder="Jelaskan detail bug yang ditemukan..." class="w-full px-3 py-2 bg-[#0b0f19] border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-indigo-500"></textarea>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-300 mb-1">Tugaskan Kepada (Assignee)</label>
                <select x-model="bugForm.assigned_to" required class="w-full px-3 py-2 bg-[#0b0f19] border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-indigo-500">
                    <option value="">-- Pilih Developer --</option>
                    @foreach($users ?? [] as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-300 mb-1">Due Date (Target Perbaikan)</label>
                <input type="date" x-model="bugForm.due_date" required min="{{ now()->format('Y-m-d') }}" class="w-full px-3 py-2 bg-[#0b0f19] border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-indigo-500">
            </div>
            <div class="flex justify-end space-x-2 pt-2">
                <button type="button" @click="showBugModal = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs text-slate-300 cursor-pointer">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-xs text-white font-semibold cursor-pointer">Simpan & Buat Tiket Bug</button>
            </div>
        </form>
    </div>
</div>
<script>
document.querySelectorAll('.progress-bar').forEach(function (bar) {
    bar.style.width = bar.dataset.width + '%';
});
 </script>
</body>
</html>