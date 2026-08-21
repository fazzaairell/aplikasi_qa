@extends('layouts.topbar')

@section('title', 'Dashboard Developer - QA Platform')

@section('hero')
    <div class="text-[10px] text-indigo-400 font-bold tracking-widest uppercase mb-1">WORKSPACE</div>
    <h1 class="text-3xl font-bold text-white tracking-tight">Developer Workspace</h1>
    <p class="text-sm text-slate-400 mt-1">Ini daftar bug yang perlu kamu tangani.</p>
@endsection

@section('content')

<style>
    select.status-open     { border-color: rgba(239,68,68,0.5);   color: #fca5a5; }
    select.status-progress { border-color: rgba(99,102,241,0.5);  color: #a5b4fc; }
    select.status-resolved { border-color: rgba(16,185,129,0.5);  color: #6ee7b7; }
    select.status-closed   { border-color: rgba(100,116,139,0.5); color: #94a3b8; }
    select.status-reopened { border-color: rgba(168,85,247,0.5);  color: #d8b4fe; }
    tr.bug-row:hover td    { background: rgba(99,102,241,0.04); }
    .filter-btn { white-space: nowrap; }
    .filter-btn.active {
        background: rgba(99,102,241,0.15) !important;
        color: #818cf8 !important;
        border-color: rgba(99,102,241,0.35) !important;
    }
    .filter-scroll { overflow-x: auto; -ms-overflow-style: none; scrollbar-width: none; }
    .filter-scroll::-webkit-scrollbar { display: none; }
</style>


@php
    $countOpen     = $bugs->where('status', 'Open')->count();
    $countProgress = $bugs->where('status', 'In Progress')->count();
    $countResolved = $bugs->where('status', 'Resolved')->count();
    $countReopened = $bugs->where('status', 'Reopened')->count();
    $countClosed   = $bugs->where('status', 'Closed')->count();
@endphp

<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 mb-6">
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

{{-- ═══════════════════════════════════════════════
     FILTER PILLS
════════════════════════════════════════════════ --}}
<div class="filter-scroll mb-4">
    <div class="flex items-center gap-2 pb-1" style="min-width: max-content;">
        <button onclick="devFilter('All')"         id="df-all"          class="filter-btn px-3.5 py-1.5 rounded-xl text-xs font-semibold border border-white/[0.06] text-slate-400 hover:text-white transition cursor-pointer" style="background:#111827;">All</button>
        <button onclick="devFilter('Open')"        id="df-open"         class="filter-btn px-3.5 py-1.5 rounded-xl text-xs font-semibold border border-white/[0.06] text-slate-400 hover:text-white transition cursor-pointer" style="background:#111827;">Open</button>
        <button onclick="devFilter('In Progress')" id="df-in-progress"  class="filter-btn px-3.5 py-1.5 rounded-xl text-xs font-semibold border border-white/[0.06] text-slate-400 hover:text-white transition cursor-pointer" style="background:#111827;">In Progress</button>
        <button onclick="devFilter('Resolved')"    id="df-resolved"     class="filter-btn px-3.5 py-1.5 rounded-xl text-xs font-semibold border border-white/[0.06] text-slate-400 hover:text-white transition cursor-pointer" style="background:#111827;">Resolved</button>
        <button onclick="devFilter('Closed')"      id="df-closed"       class="filter-btn px-3.5 py-1.5 rounded-xl text-xs font-semibold border border-white/[0.06] text-slate-400 hover:text-white transition cursor-pointer" style="background:#111827;">Closed</button>
        <button onclick="devFilter('Reopened')"    id="df-reopened"     class="filter-btn px-3.5 py-1.5 rounded-xl text-xs font-semibold border border-white/[0.06] text-slate-400 hover:text-white transition cursor-pointer" style="background:#111827;">Reopened</button>
    </div>
</div>

{{-- ═══════════════════════════════════════════════
     TABEL BUG (identik dengan bugs/index.blade.php)
════════════════════════════════════════════════ --}}
<div class="rounded-2xl border border-white/[0.06] overflow-hidden" style="background:#111827;">
    <div class="overflow-x-auto">
        <table class="w-full text-xs" style="min-width: 900px;" id="dev-bugs-table">
            <thead>
                <tr class="border-b border-white/[0.06]" style="background:rgba(12,15,26,0.6);">
                    <th class="px-4 py-3.5 text-left text-slate-400 font-semibold uppercase tracking-wider whitespace-nowrap w-12">No</th>
                    <th class="px-4 py-3.5 text-left text-slate-400 font-semibold uppercase tracking-wider whitespace-nowrap min-w-[200px]">Judul Bug</th>
                    <th class="px-4 py-3.5 text-left text-slate-400 font-semibold uppercase tracking-wider whitespace-nowrap min-w-[130px]">Project</th>
                    <th class="px-4 py-3.5 text-left text-slate-400 font-semibold uppercase tracking-wider whitespace-nowrap min-w-[100px]">Due Date</th>
                    <th class="px-4 py-3.5 text-left text-slate-400 font-semibold uppercase tracking-wider whitespace-nowrap min-w-[100px]">Finish Date</th>
                    <th class="px-4 py-3.5 text-left text-slate-400 font-semibold uppercase tracking-wider whitespace-nowrap min-w-[85px]">Priority</th>
                    <th class="px-4 py-3.5 text-left text-slate-400 font-semibold uppercase tracking-wider whitespace-nowrap min-w-[110px]">Status</th>
                    <th class="px-4 py-3.5 text-left text-slate-400 font-semibold uppercase tracking-wider whitespace-nowrap min-w-[90px]">Dibuat</th>
                    <th class="px-4 py-3.5 text-left text-slate-400 font-semibold uppercase tracking-wider whitespace-nowrap min-w-[80px]">Aksi</th>
                </tr>
            </thead>
            <tbody id="dev-bugs-tbody">
                @if($bugs->isEmpty())
                <tr id="dev-empty-row">
                    <td colspan="11" class="px-4 py-20 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center"
                                 style="background:rgba(99,102,241,0.1);border:1px solid rgba(99,102,241,0.2);">
                                <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="text-slate-400 text-sm font-medium">Tidak ada bug yang ditugaskan ke kamu</div>
                            <div class="text-slate-600 text-xs">Semuanya bersih! Tidak ada bug aktif.</div>
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
                        <div class="font-semibold text-white leading-snug truncate max-w-[200px]"
                             title="{{ $bug->title }}">{{ $bug->title }}</div>
                        @if($bug->description)
                        <div class="text-slate-500 text-[10px] leading-tight truncate max-w-[200px] mt-0.5"
                             title="{{ $bug->description }}">{{ $bug->description }}</div>
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

                    {{-- STATUS DROPDOWN --}}
                    <td class="px-4 py-3.5 whitespace-nowrap" onclick="event.stopPropagation()">
                        <form action="{{ route('bugs.update-status', $bug->id) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <select name="status" onchange="this.form.submit()"
                                    class="px-2.5 py-1.5 rounded-lg text-[10px] font-bold cursor-pointer outline-none border-2 transition-all"
                                    style="background:#0c0f1a;"
                                    title="Ubah status"
                                    @if(in_array($bug->status, ['Resolved', 'Closed', 'Reopened'])) disabled @endif>
                                <option value="Open" {{ $bug->status === 'Open' ? 'selected' : '' }} style="background:#111827;" disabled>Open</option>
                                <option value="In Progress" {{ $bug->status === 'In Progress' ? 'selected' : '' }} style="background:#111827;">In Progress</option>
                                <option value="Done in Review" {{ $bug->status === 'Done in Review' ? 'selected' : '' }} style="background:#111827;">Done in Review</option>
                                @if(in_array($bug->status, ['Resolved', 'Closed', 'Reopened']))
                                    <option value="{{ $bug->status }}" selected style="background:#111827;">{{ $bug->status }}</option>
                                @endif
                            </select>
                        </form>
                    </td>

                    {{-- DIBUAT --}}
                    <td class="px-4 py-3.5 whitespace-nowrap">
                        <span class="text-slate-500 font-mono text-[10px]">{{ $bug->created_at->format('d M Y') }}</span>
                    </td>

                    {{-- AKSI --}}
                    <td class="px-4 py-3.5 whitespace-nowrap text-right">
                        <a href="{{ route('bugs.show', $bug->id) }}" class="px-3 py-1.5 bg-[#0b0f19] border border-slate-700/80 hover:bg-indigo-600/20 text-indigo-400 hover:text-indigo-300 text-[10px] font-bold rounded-lg transition">Detail</a>
                    </td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
    </div>

    <!-- Empty state for filter -->
    <div id="dev-no-filter" class="hidden px-4 py-20 text-center">
        <div class="flex flex-col items-center gap-3">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center"
                 style="background:rgba(99,102,241,0.1);border:1px solid rgba(99,102,241,0.2);">
                <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                </svg>
            </div>
            <div class="text-slate-400 text-sm font-medium" id="dev-no-filter-msg">Tidak ada bug dengan status ini</div>
        </div>
    </div>

    <!-- Footer -->
    <div class="px-4 py-3 border-t border-white/[0.04] flex items-center justify-between">
        <span class="text-[11px] text-slate-500" id="dev-row-count">Menampilkan {{ $bugs->count() }} bug</span>
        <a href="{{ route('bugs.index') }}" class="text-[11px] text-indigo-400 hover:text-indigo-300 font-semibold">
            Lihat semua bug &rarr;
        </a>
    </div>
</div>

<script>
    let devFilter_current = 'All';

    function devFilter(status) {
        devFilter_current = status;
        document.querySelectorAll('#content-area .filter-btn').forEach(b => b.classList.remove('active'));
        const slug = status === 'In Progress' ? 'in-progress' : status.toLowerCase();
        const btn = document.getElementById('df-' + slug);
        if (btn) btn.classList.add('active');
        devApplyFilters();
    }

    function devApplyFilters() {
        const rows = document.querySelectorAll('#dev-bugs-tbody .bug-row');
        let visible = 0;
        rows.forEach(row => {
            const match = devFilter_current === 'All' || row.dataset.status === devFilter_current;
            row.style.display = match ? '' : 'none';
            if (match) visible++;
        });

        const noFilter = document.getElementById('dev-no-filter');
        if (rows.length > 0 && visible === 0) {
            noFilter.classList.remove('hidden');
            document.getElementById('dev-no-filter-msg').textContent =
                'Tidak ada bug dengan status "' + devFilter_current + '"';
        } else {
            noFilter.classList.add('hidden');
        }

        const cnt = document.getElementById('dev-row-count');
        if (cnt) cnt.textContent = 'Menampilkan ' + visible + ' bug';

        // Re-number
        let no = 1;
        rows.forEach(row => {
            if (row.style.display !== 'none') {
                const cell = row.querySelector('td:first-child span');
                if (cell) cell.textContent = no++;
            }
        });
    }

    function colorDevSelects() {
        document.querySelectorAll('#dev-bugs-table select[name="status"]').forEach(sel => {
            sel.classList.remove('status-open','status-progress','status-resolved','status-closed','status-reopened');
            const v = sel.value;
            if      (v === 'Open')        sel.classList.add('status-open');
            else if (v === 'In Progress') sel.classList.add('status-progress');
            else if (v === 'Resolved')    sel.classList.add('status-resolved');
            else if (v === 'Closed')      sel.classList.add('status-closed');
            else if (v === 'Reopened')    sel.classList.add('status-reopened');
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        devFilter('All');
        colorDevSelects();
        document.querySelectorAll('#dev-bugs-table select[name="status"]').forEach(sel => {
            sel.addEventListener('change', () => colorDevSelects());
        });
    });
</script>

@endsection
