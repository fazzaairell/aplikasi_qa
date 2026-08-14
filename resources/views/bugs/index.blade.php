<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bug Tracker - QA Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        * { font-family: 'Inter', sans-serif; box-sizing: border-box; }
        body { background: #0c0f1a; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: #0c0f1a; }
        ::-webkit-scrollbar-thumb { background: rgba(99,102,241,0.3); border-radius: 99px; }

        /* Filter pill scroll on mobile */
        .filter-scroll { overflow-x: auto; -ms-overflow-style: none; scrollbar-width: none; }
        .filter-scroll::-webkit-scrollbar { display: none; }

        /* Active filter button */
        .filter-btn { white-space: nowrap; }
        .filter-btn.active {
            background: rgba(99,102,241,0.15);
            color: #818cf8;
            border-color: rgba(99,102,241,0.35);
        }

        /* Status dropdown color states */
        select.status-open     { border-color: rgba(239,68,68,0.5);   color: #fca5a5; }
        select.status-progress { border-color: rgba(99,102,241,0.5);  color: #a5b4fc; }
        select.status-resolved { border-color: rgba(16,185,129,0.5);  color: #6ee7b7; }
        select.status-closed   { border-color: rgba(100,116,139,0.5); color: #94a3b8; }
        select.status-reopened { border-color: rgba(168,85,247,0.5);  color: #d8b4fe; }

        /* Fade-in animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-in { animation: fadeIn 0.35s ease both; }

        /* Table row hover */
        tr.bug-row:hover td { background: rgba(99,102,241,0.04); }
    </style>
</head>
<body class="h-full text-slate-100 flex overflow-hidden"
      x-data="{ sidebarOpen: false }">

<x-sidebar />

<!-- MAIN CONTENT -->
<div class="flex-1 flex flex-col min-w-0 overflow-y-auto h-full">

    <!-- TOPBAR -->
    <header class="h-16 border-b border-white/[0.06] px-4 sm:px-8 flex items-center justify-between sticky top-0 z-30"
            style="background: rgba(12,15,26,0.85); backdrop-filter: blur(12px);">
        <div class="flex items-center gap-3">
            <button @click="$dispatch('toggle-sidebar')"
                    class="md:hidden p-2 rounded-xl text-slate-400 hover:text-white border border-white/[0.06] cursor-pointer"
                    style="background:#111827;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <div class="w-40 sm:w-64 md:w-80">
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </span>
                    <input type="text" placeholder="Cari bug..."
                           id="bug-search"
                           class="w-full pl-9 pr-3 py-2 rounded-xl text-xs text-white placeholder-slate-500 border border-white/[0.06] focus:outline-none focus:border-indigo-500/50 transition"
                           style="background:#111827;"
                           oninput="filterBugs()">
                </div>
            </div>
        </div>
    </header>

    <main class="p-4 sm:p-6 lg:p-8 space-y-5 fade-in">

        <!-- PAGE HEADER -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <div class="text-[10px] text-indigo-400 font-bold tracking-widest uppercase mb-1">PELACAKAN</div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">Bug Tracker</h1>
            </div>
            <button id="btn-laporan-bug"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-white text-sm font-semibold transition shadow-lg cursor-pointer"
                    style="background: linear-gradient(135deg,#4f46e5,#6366f1); box-shadow: 0 8px 20px rgba(79,70,229,0.25);"
                    onmouseover="this.style.transform='translateY(-1px)'" onmouseout="this.style.transform=''">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Laporan Bug
            </button>
        </div>

        <!-- STATISTICS CARDS — hitung langsung, tanpa loop Blade -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
            @php
                $countOpen       = $bugs->where('status', 'Open')->count();
                $countProgress   = $bugs->where('status', 'In Progress')->count();
                $countResolved   = $bugs->where('status', 'Resolved')->count();
                $countReopened   = $bugs->where('status', 'Reopened')->count();
                $countClosed     = $bugs->where('status', 'Closed')->count();
            @endphp

            <div class="rounded-2xl p-4 space-y-1 transition hover:scale-105 duration-200"
                 style="background:rgba(239,68,68,0.15); border:1px solid rgba(239,68,68,0.25);">
                <div class="text-2xl font-bold text-white">{{ $countOpen }}</div>
                <div class="text-xs font-semibold" style="color:#fca5a5">Open</div>
            </div>
            <div class="rounded-2xl p-4 space-y-1 transition hover:scale-105 duration-200"
                 style="background:rgba(99,102,241,0.15); border:1px solid rgba(99,102,241,0.25);">
                <div class="text-2xl font-bold text-white">{{ $countProgress }}</div>
                <div class="text-xs font-semibold" style="color:#a5b4fc">In Progress</div>
            </div>
            <div class="rounded-2xl p-4 space-y-1 transition hover:scale-105 duration-200"
                 style="background:rgba(16,185,129,0.15); border:1px solid rgba(16,185,129,0.25);">
                <div class="text-2xl font-bold text-white">{{ $countResolved }}</div>
                <div class="text-xs font-semibold" style="color:#6ee7b7">Resolved</div>
            </div>
            <div class="rounded-2xl p-4 space-y-1 transition hover:scale-105 duration-200"
                 style="background:rgba(168,85,247,0.15); border:1px solid rgba(168,85,247,0.25);">
                <div class="text-2xl font-bold text-white">{{ $countReopened }}</div>
                <div class="text-xs font-semibold" style="color:#d8b4fe">Reopened</div>
            </div>
            <div class="rounded-2xl p-4 space-y-1 transition hover:scale-105 duration-200"
                 style="background:rgba(100,116,139,0.15); border:1px solid rgba(100,116,139,0.25);">
                <div class="text-2xl font-bold text-white">{{ $countClosed }}</div>
                <div class="text-xs font-semibold" style="color:#94a3b8">Closed</div>
            </div>
        </div>

        <!-- FILTER BUTTONS -->
        <div class="filter-scroll">
            <div class="flex items-center gap-2 pb-1" id="filter-buttons" style="min-width: max-content;">
                <button onclick="setFilter('All')"        id="filter-all"         class="filter-btn px-3.5 py-1.5 rounded-xl text-xs font-semibold border border-white/[0.06] text-slate-400 hover:text-white transition cursor-pointer" style="background:#111827;">All</button>
                <button onclick="setFilter('Open')"       id="filter-open"        class="filter-btn px-3.5 py-1.5 rounded-xl text-xs font-semibold border border-white/[0.06] text-slate-400 hover:text-white transition cursor-pointer" style="background:#111827;">Open</button>
                <button onclick="setFilter('In Progress')" id="filter-in-progress" class="filter-btn px-3.5 py-1.5 rounded-xl text-xs font-semibold border border-white/[0.06] text-slate-400 hover:text-white transition cursor-pointer" style="background:#111827;">In Progress</button>
                <button onclick="setFilter('Resolved')"   id="filter-resolved"    class="filter-btn px-3.5 py-1.5 rounded-xl text-xs font-semibold border border-white/[0.06] text-slate-400 hover:text-white transition cursor-pointer" style="background:#111827;">Resolved</button>
                <button onclick="setFilter('Closed')"     id="filter-closed"      class="filter-btn px-3.5 py-1.5 rounded-xl text-xs font-semibold border border-white/[0.06] text-slate-400 hover:text-white transition cursor-pointer" style="background:#111827;">Closed</button>
                <button onclick="setFilter('Reopened')"   id="filter-reopened"    class="filter-btn px-3.5 py-1.5 rounded-xl text-xs font-semibold border border-white/[0.06] text-slate-400 hover:text-white transition cursor-pointer" style="background:#111827;">Reopened</button>
            </div>
        </div>

        <!-- BUGS TABLE -->
        <div class="rounded-2xl border border-white/[0.06] overflow-hidden" style="background:#111827;">
            <div class="overflow-x-auto">
                <table class="w-full text-xs" style="min-width: 900px;" id="bugs-table">
                    <thead>
                        <tr class="border-b border-white/[0.06]" style="background:rgba(12,15,26,0.6);">
                            <th class="px-4 py-3.5 text-left text-slate-400 font-semibold uppercase tracking-wider whitespace-nowrap w-12">No</th>
                            <th class="px-4 py-3.5 text-left text-slate-400 font-semibold uppercase tracking-wider whitespace-nowrap min-w-[200px]">Judul Bug</th>
                            <th class="px-4 py-3.5 text-left text-slate-400 font-semibold uppercase tracking-wider whitespace-nowrap min-w-[130px]">Project</th>
                            <th class="px-4 py-3.5 text-left text-slate-400 font-semibold uppercase tracking-wider whitespace-nowrap min-w-[160px]">Requirement</th>
                            <th class="px-4 py-3.5 text-left text-slate-400 font-semibold uppercase tracking-wider whitespace-nowrap min-w-[140px]">Test Suite</th>
                            <th class="px-4 py-3.5 text-left text-slate-400 font-semibold uppercase tracking-wider whitespace-nowrap min-w-[140px]">Test Case</th>
                            <th class="px-4 py-3.5 text-left text-slate-400 font-semibold uppercase tracking-wider whitespace-nowrap min-w-[100px]">Due Date</th>
                            <th class="px-4 py-3.5 text-left text-slate-400 font-semibold uppercase tracking-wider whitespace-nowrap min-w-[100px]">Finish Date</th>
                            <th class="px-4 py-3.5 text-left text-slate-400 font-semibold uppercase tracking-wider whitespace-nowrap min-w-[85px]">Priority</th>
                            <th class="px-4 py-3.5 text-left text-slate-400 font-semibold uppercase tracking-wider whitespace-nowrap min-w-[110px]">Status</th>
                            <th class="px-4 py-3.5 text-left text-slate-400 font-semibold uppercase tracking-wider whitespace-nowrap min-w-[140px]">Assigned To</th>
                            <th class="px-4 py-3.5 text-left text-slate-400 font-semibold uppercase tracking-wider whitespace-nowrap min-w-[90px]">Dibuat</th>
                        </tr>
                    </thead>
                    <tbody id="bugs-tbody">
                        @if($bugs->isEmpty())
                        <tr id="empty-row">
                            <td colspan="12" class="px-4 py-20 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background:rgba(99,102,241,0.1);border:1px solid rgba(99,102,241,0.2);">
                                        <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div class="text-slate-400 text-sm font-medium">Tidak ada bug ditemukan</div>
                                    <div class="text-slate-600 text-xs">Semua bersih! Belum ada laporan bug.</div>
                                </div>
                            </td>
                        </tr>
                        @else
                        @php $rowNum = 0; @endphp
                        @foreach($bugs as $bug)
                        @php
                            $rowNum++;
                            $projectName = $bug->testResult?->testCase?->testSuite?->project?->name ?? null;
                            $reqTitle    = $bug->testResult?->testCase?->requirement?->title ?? null;
                            $suiteName   = $bug->testResult?->testCase?->testSuite?->name ?? null;
                            $caseName    = $bug->testResult?->testCase?->title ?? null;
                            $dueDate     = $bug->due_date;
                            $priority    = $bug->testResult?->testCase?->priority ?? 'Low';
                        @endphp
                        <tr class="bug-row border-b border-white/[0.04] transition" data-status="{{ $bug->status }}">
                            {{-- NO --}}
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="text-slate-500 font-mono font-semibold">{{ $rowNum }}</span>
                            </td>

                            {{-- JUDUL BUG --}}
                            <td class="px-4 py-3.5">
                                <div class="font-semibold text-white leading-snug truncate max-w-[200px]" title="{{ $bug->title }}">{{ $bug->title }}</div>
                                @if($bug->description)
                                <div class="text-slate-500 text-[10px] leading-tight truncate max-w-[200px] mt-0.5" title="{{ $bug->description }}">{{ $bug->description }}</div>
                                @endif
                            </td>

                            {{-- PROJECT --}}
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                @if($projectName)
                                <span class="text-slate-300">{{ $projectName }}</span>
                                @else
                                <span class="text-slate-600">—</span>
                                @endif
                            </td>

                            {{-- REQUIREMENT --}}
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                @if($reqTitle)
                                <span class="text-slate-300 truncate block max-w-[160px]" title="{{ $reqTitle }}">{{ $reqTitle }}</span>
                                @else
                                <span class="text-slate-600">—</span>
                                @endif
                            </td>

                            {{-- TEST SUITE --}}
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                @if($suiteName)
                                <span class="text-slate-300 truncate block max-w-[140px]" title="{{ $suiteName }}">{{ $suiteName }}</span>
                                @else
                                <span class="text-slate-600">—</span>
                                @endif
                            </td>

                            {{-- TEST CASE --}}
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                @if($caseName)
                                <span class="text-slate-300 truncate block max-w-[140px]" title="{{ $caseName }}">{{ $caseName }}</span>
                                @else
                                <span class="text-slate-600">—</span>
                                @endif
                            </td>

                            {{-- DUE DATE --}}
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                @if($dueDate)
                                <span class="text-slate-400 font-mono text-[11px]">{{ $dueDate->format('d M Y') }}</span>
                                @else
                                <span class="text-slate-600">—</span>
                                @endif
                            </td>

                            {{-- FINISH DATE --}}
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                @if($bug->finish_date)
                                <span class="px-2 py-1 rounded-lg text-[10px] font-semibold"
                                      style="background:rgba(16,185,129,0.1); color:#6ee7b7; border:1px solid rgba(16,185,129,0.25);">
                                    {{ $bug->finish_date->format('d M Y') }}
                                </span>
                                @else
                                <span class="text-slate-600">—</span>
                                @endif
                            </td>

                            {{-- PRIORITY --}}
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                @if($priority === 'Critical')
                                    <span class="px-2 py-1 rounded-lg text-[9px] font-bold" style="background:rgba(239,68,68,0.12);color:#fca5a5;border:1px solid rgba(239,68,68,0.25);">🔴 Critical</span>
                                @elseif($priority === 'High')
                                    <span class="px-2 py-1 rounded-lg text-[9px] font-bold" style="background:rgba(249,115,22,0.12);color:#fdba74;border:1px solid rgba(249,115,22,0.25);">🟠 High</span>
                                @elseif($priority === 'Medium')
                                    <span class="px-2 py-1 rounded-lg text-[9px] font-bold" style="background:rgba(234,179,8,0.12);color:#fde047;border:1px solid rgba(234,179,8,0.25);">🟡 Medium</span>
                                @else
                                    <span class="px-2 py-1 rounded-lg text-[9px] font-bold" style="background:rgba(100,116,139,0.12);color:#94a3b8;border:1px solid rgba(100,116,139,0.25);">⬜ Low</span>
                                @endif
                            </td>

                            {{-- STATUS DROPDOWN — hardcoded options, NO nested @foreach --}}
                            <td class="px-4 py-3.5 whitespace-nowrap" onclick="event.stopPropagation()">
                                <form action="{{ route('bugs.update-status', $bug->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" onchange="this.form.submit()"
                                            class="px-2.5 py-1.5 rounded-lg text-[10px] font-bold cursor-pointer outline-none border-2 transition-all"
                                            style="background:#0c0f1a;"
                                            title="Ubah status">
                                        <option value="Open"           {{ $bug->status === 'Open'           ? 'selected' : '' }} style="background:#111827;">Open</option>
                                        <option value="In Progress"   {{ $bug->status === 'In Progress'   ? 'selected' : '' }} style="background:#111827;">In Progress</option>
                                        <option value="Done in Review" {{ $bug->status === 'Done in Review' ? 'selected' : '' }} style="background:#111827;">Done in Review</option>
                                        <option value="Resolved"      {{ $bug->status === 'Resolved'      ? 'selected' : '' }} style="background:#111827;">Resolved</option>
                                        <option value="Closed"        {{ $bug->status === 'Closed'        ? 'selected' : '' }} style="background:#111827;">Closed</option>
                                        <option value="Reopened"      {{ $bug->status === 'Reopened'      ? 'selected' : '' }} style="background:#111827;">Reopened</option>
                                    </select>
                                </form>
                            </td>

                            {{-- ASSIGNED TO --}}
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                @if($bug->assignee)
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-lg flex items-center justify-center text-white text-[9px] font-bold shrink-0"
                                         style="background: linear-gradient(135deg,#4f46e5,#7c3aed);">
                                        {{ strtoupper(substr($bug->assignee->name, 0, 2)) }}
                                    </div>
                                    <span class="text-slate-300 truncate max-w-[100px]" title="{{ $bug->assignee->name }}">{{ $bug->assignee->name }}</span>
                                </div>
                                @else
                                <span class="text-slate-600">—</span>
                                @endif
                            </td>

                            {{-- DIBUAT --}}
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="text-slate-500 font-mono text-[10px]">{{ $bug->created_at->format('d M Y') }}</span>
                            </td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Empty state for filter -->
            <div id="no-filter-result" class="hidden px-4 py-20 text-center">
                <div class="flex flex-col items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background:rgba(99,102,241,0.1);border:1px solid rgba(99,102,241,0.2);">
                        <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                        </svg>
                    </div>
                    <div class="text-slate-400 text-sm font-medium" id="no-filter-msg">Tidak ada bug dengan status ini</div>
                </div>
            </div>

            <!-- Footer count -->
            <div class="px-4 py-3 border-t border-white/[0.04] flex items-center justify-between">
                <span class="text-[11px] text-slate-500" id="row-count">Menampilkan {{ $bugs->count() }} bug</span>
                <span class="text-[11px] text-slate-600">Total: {{ $bugs->count() }} data</span>
            </div>
        </div>

    </main>
</div>

<x-profile-modal />

<script>
    let currentFilter = 'All';
    let currentSearch = '';

    function setFilter(status) {
        currentFilter = status;
        document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
        const slug = status === 'In Progress' ? 'in-progress' : status.toLowerCase();
        const btn = document.getElementById('filter-' + slug);
        if (btn) btn.classList.add('active');
        applyFilters();
    }

    function filterBugs() {
        currentSearch = document.getElementById('bug-search').value.toLowerCase().trim();
        applyFilters();
    }

    function applyFilters() {
        const rows = document.querySelectorAll('.bug-row');
        let visible = 0;

        rows.forEach(row => {
            const status = row.dataset.status || '';
            const text   = row.innerText.toLowerCase();
            const matchFilter = currentFilter === 'All' || status === currentFilter;
            const matchSearch = currentSearch === '' || text.includes(currentSearch);
            if (matchFilter && matchSearch) {
                row.style.display = '';
                visible++;
            } else {
                row.style.display = 'none';
            }
        });

        const noResult = document.getElementById('no-filter-result');
        if (rows.length > 0 && visible === 0) {
            noResult.classList.remove('hidden');
            document.getElementById('no-filter-msg').textContent =
                currentFilter !== 'All'
                    ? 'Tidak ada bug dengan status "' + currentFilter + '"'
                    : 'Tidak ada bug yang cocok dengan pencarian';
        } else {
            noResult.classList.add('hidden');
        }

        const countEl = document.getElementById('row-count');
        if (countEl) countEl.textContent = 'Menampilkan ' + visible + ' bug';

        let no = 1;
        rows.forEach(row => {
            if (row.style.display !== 'none') {
                const noCell = row.querySelector('td:first-child span');
                if (noCell) noCell.textContent = no++;
            }
        });
    }

    function colorStatusSelects() {
        document.querySelectorAll('select[name="status"]').forEach(sel => {
            sel.classList.remove('status-open','status-progress','status-resolved','status-closed','status-reopened');
            const v = sel.value;
            if (v === 'Open')            sel.classList.add('status-open');
            else if (v === 'In Progress') sel.classList.add('status-progress');
            else if (v === 'Resolved')    sel.classList.add('status-resolved');
            else if (v === 'Closed')      sel.classList.add('status-closed');
            else if (v === 'Reopened')    sel.classList.add('status-reopened');
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        setFilter('All');
        colorStatusSelects();
        document.querySelectorAll('select[name="status"]').forEach(sel => {
            sel.addEventListener('change', () => colorStatusSelects());
        });
    });
</script>
<x-profile-modal />
</body>
</html>
