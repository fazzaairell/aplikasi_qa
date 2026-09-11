<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Developer - TESTIFY</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>* { font-family: 'Inter', sans-serif; } body { background: #0c0f1a; } ::-webkit-scrollbar{width:5px;height:5px} ::-webkit-scrollbar-track{background:#0c0f1a} ::-webkit-scrollbar-thumb{background:rgba(99,102,241,.3);border-radius:99px}

    select.status-open { border-color: rgba(239,68,68,0.5); color: #fca5a5; }
    select.status-progress { border-color: rgba(99,102,241,0.5); color: #a5b4fc; }
    select.status-resolved { border-color: rgba(16,185,129,0.5); color: #6ee7b7; }
    select.status-closed { border-color: rgba(100,116,139,0.5); color: #94a3b8; }
    select.status-reopened { border-color: rgba(168,85,247,0.5); color: #d8b4fe; }
    tr.bug-row:hover td { background: rgba(99,102,241,0.04); }
    .filter-btn { white-space: nowrap; }
    .filter-btn.active { background: rgba(99,102,241,0.15) !important; color: #818cf8 !important; border-color: rgba(99,102,241,0.35) !important; }
    .filter-scroll { overflow-x: auto; -ms-overflow-style: none; scrollbar-width: none; }
    .filter-scroll::-webkit-scrollbar { display: none; }
    select[name="status"] { background-color: #0c0f1a !important; color: #e2e8f0 !important; }
    select[name="status"] option { background-color: #0c0f1a !important; color: #e2e8f0 !important; }
    input.date-editable { color-scheme: dark; }
    input.date-editable::-webkit-calendar-picker-indicator { filter: invert(0.6); cursor: pointer; }
    </style>
</head>
<body class="h-full font-sans text-slate-100 flex overflow-hidden" x-data="{ sidebarOpen: false }">

    <x-sidebar />

    <!-- MAIN CONTENT -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto h-full">

        <main class="p-6 sm:p-8 space-y-6 max-w-screen-2xl mx-auto w-full">




@php

    $countOpen = $bugs->where('status', 'Open')->count();
    $countProgress = $bugs->where('status', 'In Progress')->count();
    $countResolved = $bugs->where('status', 'Resolved')->count();
    $countReopened = $bugs->where('status', 'Reopened')->count();
    $countReview = $bugs->where('status', 'Done in Review')->count();

@endphp


{{-- ═══════════════════════════════════════════════
     STATISTICS
════════════════════════════════════════════════ --}}

<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 mb-6">

    <div class="rounded-2xl p-4 space-y-1 transition hover:scale-105 duration-200"
         style="background:rgba(239,68,68,0.15); border:1px solid rgba(239,68,68,0.25);">

        <div class="text-2xl font-bold text-white">
            {{ $countOpen }}
        </div>

        <div class="text-xs font-semibold" style="color:#fca5a5">
            Open
        </div>

    </div>


    <div class="rounded-2xl p-4 space-y-1 transition hover:scale-105 duration-200"
         style="background:rgba(99,102,241,0.15); border:1px solid rgba(99,102,241,0.25);">

        <div class="text-2xl font-bold text-white">
            {{ $countProgress }}
        </div>

        <div class="text-xs font-semibold" style="color:#a5b4fc">
            In Progress
        </div>

    </div>


    <div class="rounded-2xl p-4 space-y-1 transition hover:scale-105 duration-200"
         style="background:rgba(16,185,129,0.15); border:1px solid rgba(16,185,129,0.25);">

        <div class="text-2xl font-bold text-white">
            {{ $countResolved }}
        </div>

        <div class="text-xs font-semibold" style="color:#6ee7b7">
            Resolved
        </div>

    </div>


    <div class="rounded-2xl p-4 space-y-1 transition hover:scale-105 duration-200"
         style="background:rgba(168,85,247,0.15); border:1px solid rgba(168,85,247,0.25);">

        <div class="text-2xl font-bold text-white">
            {{ $countReopened }}
        </div>

        <div class="text-xs font-semibold" style="color:#d8b4fe">
            Reopened
        </div>

    </div>


    {{-- FIX: label disamakan dengan status aslinya "Done in Review"
         (sebelumnya tertulis "Closed" yang membingungkan, karena status
         "Closed" itu sendiri adalah konsep yang berbeda) --}}
    <div class="rounded-2xl p-4 space-y-1 transition hover:scale-105 duration-200"
         style="background:rgba(100,116,139,0.15); border:1px solid rgba(100,116,139,0.25);">

        <div class="text-2xl font-bold text-white">
            {{ $countReview }}
        </div>

        <div class="text-xs font-semibold" style="color:#94a3b8">
            Done in Review
        </div>

    </div>

</div>


{{-- ═══════════════════════════════════════════════
     FILTER PILLS
════════════════════════════════════════════════ --}}

<div class="filter-scroll mb-4">

    <div class="flex items-center gap-2 pb-1" style="min-width: max-content;">

        <button
            onclick="devFilter('All')"
            id="df-all"
            class="filter-btn px-3.5 py-1.5 rounded-xl text-xs font-semibold border border-white/[0.06] text-slate-400 hover:text-white transition cursor-pointer"
            style="background:#111827;">
            All
        </button>


        <button
            onclick="devFilter('Open')"
            id="df-open"
            class="filter-btn px-3.5 py-1.5 rounded-xl text-xs font-semibold border border-white/[0.06] text-slate-400 hover:text-white transition cursor-pointer"
            style="background:#111827;">
            Open
        </button>


        <button
            onclick="devFilter('In Progress')"
            id="df-in-progress"
            class="filter-btn px-3.5 py-1.5 rounded-xl text-xs font-semibold border border-white/[0.06] text-slate-400 hover:text-white transition cursor-pointer"
            style="background:#111827;">
            In Progress
        </button>


        <button
            onclick="devFilter('Resolved')"
            id="df-resolved"
            class="filter-btn px-3.5 py-1.5 rounded-xl text-xs font-semibold border border-white/[0.06] text-slate-400 hover:text-white transition cursor-pointer"
            style="background:#111827;">
            Resolved
        </button>


        <button
            onclick="devFilter('Done in Review')"
            id="df-closed"
            class="filter-btn px-3.5 py-1.5 rounded-xl text-xs font-semibold border border-white/[0.06] text-slate-400 hover:text-white transition cursor-pointer"
            style="background:#111827;">
            Closed
        </button>


        <button
            onclick="devFilter('Reopened')"
            id="df-reopened"
            class="filter-btn px-3.5 py-1.5 rounded-xl text-xs font-semibold border border-white/[0.06] text-slate-400 hover:text-white transition cursor-pointer"
            style="background:#111827;">
            Reopened
        </button>

    </div>

</div>


{{-- ═══════════════════════════════════════════════
     TABEL BUG
════════════════════════════════════════════════ --}}

<div class="rounded-2xl border border-white/[0.06] overflow-hidden"
     style="background:#111827;">

    <div class="overflow-x-auto">

        <table
            class="w-full text-xs"
            style="min-width: 900px;"
            id="dev-bugs-table">

            <thead>

                <tr
                    class="border-b border-white/[0.06]"
                    style="background:rgba(12,15,26,0.6);">

                    <th class="px-4 py-3.5 text-left text-slate-400 font-semibold uppercase tracking-wider whitespace-nowrap w-12">
                        No
                    </th>

                    <th class="px-4 py-3.5 text-left text-slate-400 font-semibold uppercase tracking-wider whitespace-nowrap min-w-[200px]">
                        Judul Bug
                    </th>

                    <th class="px-4 py-3.5 text-left text-slate-400 font-semibold uppercase tracking-wider whitespace-nowrap min-w-[130px]">
                        Project
                    </th>

                    <th class="px-4 py-3.5 text-left text-slate-400 font-semibold uppercase tracking-wider whitespace-nowrap min-w-[100px]">
                        DUE DATE
                    </th>

                    <th class="px-4 py-3.5 text-left text-slate-400 font-semibold uppercase tracking-wider whitespace-nowrap min-w-[100px]">
                        Start Date
                    </th>

                    <th class="px-4 py-3.5 text-left text-slate-400 font-semibold uppercase tracking-wider whitespace-nowrap min-w-[100px]">
                        Finish Date
                    </th>

                    <th class="px-4 py-3.5 text-left text-slate-400 font-semibold uppercase tracking-wider whitespace-nowrap min-w-[85px]">
                        Priority
                    </th>

                    <th class="px-4 py-3.5 text-left text-slate-400 font-semibold uppercase tracking-wider whitespace-nowrap min-w-[110px]">
                        Status
                    </th>

                    <th class="px-4 py-3.5 text-left text-slate-400 font-semibold uppercase tracking-wider whitespace-nowrap min-w-[90px]">
                        Dibuat
                    </th>

                    <th class="px-4 py-3.5 text-left text-slate-400 font-semibold uppercase tracking-wider whitespace-nowrap min-w-[80px]">
                        Aksi
                    </th>

                </tr>

            </thead>


            <tbody id="dev-bugs-tbody">

                @if($bugs->isEmpty())

                    <tr id="dev-empty-row">

                        <td colspan="9" class="px-4 py-20 text-center">

                            <div class="flex flex-col items-center gap-3">

                                <div
                                    class="w-12 h-12 rounded-2xl flex items-center justify-center"
                                    style="background:rgba(99,102,241,0.1);border:1px solid rgba(99,102,241,0.2);">

                                    <svg
                                        class="w-6 h-6 text-indigo-400"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24">

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="1.5"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>

                                    </svg>

                                </div>


                                <div class="text-slate-400 text-sm font-medium">
                                    Tidak ada bug yang ditugaskan ke kamu
                                </div>


                                <div class="text-slate-600 text-xs">
                                    Semuanya bersih! Tidak ada bug aktif.
                                </div>

                            </div>

                        </td>

                    </tr>

                @else

                    @php
                        $rowNum = 0;
                    @endphp


                    @foreach($bugs as $bug)

                        @php

                            $rowNum++;

                            $projectName =
                                $bug->testResult?->testCase?->testSuite?->project?->name ?? null;

                            $dueDate =
                                $bug->due_date;

                            $priority =
                                $bug->testResult?->testCase?->priority ?? 'Low';

                        @endphp


                        <tr
                            class="bug-row border-b border-white/[0.04] transition"
                            data-status="{{ $bug->status }}">

                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="text-slate-500 font-mono font-semibold">
                                    {{ $rowNum }}
                                </span>
                            </td>

                            <td class="px-4 py-3.5">
                                <div
                                    class="font-semibold text-white leading-snug truncate max-w-[200px]"
                                    title="{{ $bug->title }}">
                                    {{ $bug->title }}
                                </div>

                                @if($bug->description)
                                    <div
                                        class="text-slate-500 text-[10px] leading-tight truncate max-w-[200px] mt-0.5"
                                        title="{{ $bug->description }}">
                                        {{ $bug->description }}
                                    </div>
                                @endif
                            </td>

                            <td class="px-4 py-3.5 whitespace-nowrap">
                                @if($projectName)
                                    <span class="text-slate-300">{{ $projectName }}</span>
                                @else
                                    <span class="text-slate-600">—</span>
                                @endif
                            </td>

                            <td class="px-4 py-3.5 whitespace-nowrap">
                                @if($dueDate)
                                    <span class="text-slate-400 font-mono text-[11px]">{{ $dueDate->format('d M Y') }}</span>
                                @else
                                    <span class="text-slate-600">—</span>
                                @endif
                            </td>

                            {{-- Start Date: sekarang bisa diisi/diubah manual langsung dari tabel --}}
                            <td class="px-4 py-3.5 whitespace-nowrap" onclick="event.stopPropagation()">
                                <form
                                    action="{{ route('bugs.update-status', $bug->id) }}"
                                    method="POST"
                                    class="inline-flex">

                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="{{ $bug->status }}">

                                    <input
                                        type="date"
                                        name="start_date"
                                        value="{{ $bug->start_date?->format('Y-m-d') }}"
                                        onchange="this.form.submit()"
                                        class="date-editable px-2 py-1 rounded-lg text-[10px] font-semibold outline-none border transition"
                                        style="background:rgba(99,102,241,0.1); color:#a5b4fc; border-color:rgba(99,102,241,0.25);"
                                        title="Klik untuk mengisi/mengubah Start Date">
                                </form>
                            </td>

                            {{-- Finish Date: sekarang bisa diisi/diubah manual langsung dari tabel --}}
                            <td class="px-4 py-3.5 whitespace-nowrap" onclick="event.stopPropagation()">
                                <form
                                    action="{{ route('bugs.update-status', $bug->id) }}"
                                    method="POST"
                                    class="inline-flex">

                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="{{ $bug->status }}">

                                    <input
                                        type="date"
                                        name="finish_date"
                                        value="{{ $bug->finish_date?->format('Y-m-d') }}"
                                        onchange="this.form.submit()"
                                        class="date-editable px-2 py-1 rounded-lg text-[10px] font-semibold outline-none border transition"
                                        style="background:rgba(16,185,129,0.1); color:#6ee7b7; border-color:rgba(16,185,129,0.25);"
                                        title="Klik untuk mengisi/mengubah Finish Date">
                                </form>
                            </td>

                            <td class="px-4 py-3.5 whitespace-nowrap">
                                @if($priority === 'Critical')
                                    <span
                                        class="px-2 py-1 rounded-lg text-[9px] font-bold"
                                        style="background:rgba(239,68,68,0.12);color:#fca5a5;border:1px solid rgba(239,68,68,0.25);">
                                        🔴 Critical
                                    </span>
                                @elseif($priority === 'High')
                                    <span
                                        class="px-2 py-1 rounded-lg text-[9px] font-bold"
                                        style="background:rgba(249,115,22,0.12);color:#fdba74;border:1px solid rgba(249,115,22,0.25);">
                                        🟠 High
                                    </span>
                                @elseif($priority === 'Medium')
                                    <span
                                        class="px-2 py-1 rounded-lg text-[9px] font-bold"
                                        style="background:rgba(234,179,8,0.12);color:#fde047;border:1px solid rgba(234,179,8,0.25);">
                                        🟡 Medium
                                    </span>
                                @else
                                    <span
                                        class="px-2 py-1 rounded-lg text-[9px] font-bold"
                                        style="background:rgba(100,116,139,0.12);color:#94a3b8;border:1px solid rgba(100,116,139,0.25);">
                                        ⬜ Low
                                    </span>
                                @endif
                            </td>

                            <td
                                class="px-4 py-3.5 whitespace-nowrap"
                                onclick="event.stopPropagation()">

                                <form
                                    action="{{ route('bugs.update-status', $bug->id) }}"
                                    method="POST"
                                    class="inline">

                                    @csrf
                                    @method('PATCH')

                                    <select
                                        name="status"
                                        data-current-status="{{ $bug->status }}"
                                        onchange="handleDevBugStatusChange(this)"
                                        class="px-2.5 py-1.5 rounded-lg text-[10px] font-bold cursor-pointer outline-none border-2 transition-all bg-[#0c0f1a]"
                                        title="Ubah status"
                                        @if(in_array($bug->status, ['Resolved', 'Reopened']))
                                            disabled
                                        @endif>

                                        <option value="Open" {{ $bug->status === 'Open' ? 'selected' : '' }} style="background:#0c0f1a; color:#fca5a5;" disabled>
                                            Open
                                        </option>

                                        <option value="In Progress" {{ $bug->status === 'In Progress' ? 'selected' : '' }} style="background:#0c0f1a; color:#a5b4fc;">
                                            In Progress
                                        </option>

                                        <option value="Done in Review" {{ $bug->status === 'Done in Review' ? 'selected' : '' }} style="background:#0c0f1a; color:#e2e8f0;">
                                            Done in Review
                                        </option>

                                        @if(in_array($bug->status, ['Resolved', 'Reopened']))
                                            <option value="{{ $bug->status }}" selected style="background:#0c0f1a; color:#e2e8f0;">
                                                {{ $bug->status }}
                                            </option>
                                        @endif

                                    </select>

                                </form>

                            </td>

                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="text-slate-500 font-mono text-[10px]">{{ $bug->created_at->format('d M Y') }}</span>
                            </td>

                            <td class="px-4 py-3.5 whitespace-nowrap text-right">
                                <a
                                    href="{{ route('bugs.show', $bug->id) }}"
                                    class="px-3 py-1.5 bg-[#0b0f19] border border-slate-700/80 hover:bg-indigo-600/20 text-indigo-400 hover:text-indigo-300 text-[10px] font-bold rounded-lg transition">
                                    Detail
                                </a>
                            </td>

                        </tr>

                    @endforeach

                @endif

            </tbody>

        </table>

    </div>


    {{-- Empty state for filter --}}

    <div
        id="dev-no-filter"
        class="hidden px-4 py-20 text-center">

        <div class="flex flex-col items-center gap-3">

            <div
                class="w-12 h-12 rounded-2xl flex items-center justify-center"
                style="background:rgba(99,102,241,0.1);border:1px solid rgba(99,102,241,0.2);">

                <svg
                    class="w-6 h-6 text-indigo-400"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.5"
                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>

                </svg>

            </div>

            <div class="text-slate-400 text-sm font-medium" id="dev-no-filter-msg">
                Tidak ada bug dengan status ini
            </div>

        </div>

    </div>


    {{-- Footer --}}

    <div class="px-4 py-3 border-t border-white/[0.04] flex items-center justify-between">

        <span class="text-[11px] text-slate-500" id="dev-row-count">
            Menampilkan {{ $bugs->count() }} bug
        </span>

        <a
            href="{{ route('bugs.index') }}"
            class="text-[11px] text-indigo-400 hover:text-indigo-300 font-semibold">
            Lihat semua bug &rarr;
        </a>

    </div>

</div>


<script>

    let devFilter_current = 'All';

    // FIX: mapping slug eksplisit. Sebelumnya status.toLowerCase() untuk
    // "Done in Review" menghasilkan "done in review" (ada spasi), padahal
    // id tombolnya "df-closed" — akibatnya tombol filter tidak pernah
    // ter-highlight walau filter-nya tetap berfungsi.
    const devFilterSlugMap = {
        'All': 'all',
        'Open': 'open',
        'In Progress': 'in-progress',
        'Resolved': 'resolved',
        'Done in Review': 'closed',
        'Reopened': 'reopened',
    };

    function devFilter(status) {

        devFilter_current = status;

        document
            .querySelectorAll('.filter-btn')
            .forEach(b => b.classList.remove('active'));

        const slug = devFilterSlugMap[status] ?? status.toLowerCase();

        const btn = document.getElementById('df-' + slug);

        if (btn) {
            btn.classList.add('active');
        }

        devApplyFilters();

    }


    function devApplyFilters() {

        const rows =
            document.querySelectorAll('#dev-bugs-tbody .bug-row');

        let visible = 0;

        rows.forEach(row => {

            const match =
                devFilter_current === 'All' ||
                row.dataset.status === devFilter_current;

            row.style.display = match ? '' : 'none';

            if (match) {
                visible++;
            }

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

        if (cnt) {
            cnt.textContent = 'Menampilkan ' + visible + ' bug';
        }

        // Re-number
        let no = 1;

        rows.forEach(row => {

            if (row.style.display !== 'none') {

                const cell = row.querySelector('td:first-child span');

                if (cell) {
                    cell.textContent = no++;
                }

            }

        });

    }


    function colorDevSelects() {

        document
            .querySelectorAll('#dev-bugs-table select[name="status"]')
            .forEach(sel => {

                sel.classList.remove(
                    'status-open',
                    'status-progress',
                    'status-resolved',
                    'status-closed',
                    'status-reopened'
                );

                const v = sel.value;

                if (v === 'Open') {
                    sel.classList.add('status-open');
                } else if (v === 'In Progress') {
                    sel.classList.add('status-progress');
                } else if (v === 'Resolved') {
                    sel.classList.add('status-resolved');
                } else if (v === 'Done in Review') {
                    sel.classList.add('status-closed');
                } else if (v === 'Reopened') {
                    sel.classList.add('status-reopened');
                }

            });

    }


    document.addEventListener('DOMContentLoaded', () => {

        devFilter('All');

        colorDevSelects();

        document
            .querySelectorAll('#dev-bugs-table select[name="status"]')
            .forEach(sel => {

                sel.addEventListener('change', () => {
                    colorDevSelects();
                });

            });

    });

    let fixAttachmentTargetForm = null;
    let fixAttachmentSelect = null;

    function todayIsoDate() {
        const d = new Date();
        const pad = (n) => String(n).padStart(2, '0');
        return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
    }

    // FIX: Start Date & Finish Date sekarang diisi langsung dari kolom
    // tabelnya masing-masing (input type="date"), jadi popup konfirmasi
    // tanggal saat ganti status sudah tidak diperlukan lagi. Modal hanya
    // dipakai untuk upload bukti perbaikan (opsional) saat status
    // diubah ke "Done in Review".
function handleDevBugStatusChange(selectEl) {
    if (selectEl.value === 'Done in Review') {
        // cuma modal upload bukti perbaikan (opsional), TIDAK ada input tanggal di sini
        fixAttachmentTargetForm = selectEl.form;
        fixAttachmentSelect = selectEl;
        document.getElementById('fixAttachmentInput').value = '';
        document.getElementById('fixAttachmentModal').classList.remove('hidden');
        document.getElementById('fixAttachmentModal').classList.add('flex');
    } else {
        // "In Progress" dan status lain: langsung submit, tanpa trigger apapun ke start/finish date
        selectEl.form.submit();
    }
}

    function closeFixAttachmentModal() {
        document.getElementById('fixAttachmentModal').classList.add('hidden');
        document.getElementById('fixAttachmentModal').classList.remove('flex');
        if (fixAttachmentSelect) {
            fixAttachmentSelect.value = fixAttachmentSelect.dataset.currentStatus;
            colorDevSelects();
        }
        fixAttachmentTargetForm = null;
        fixAttachmentSelect = null;
    }

    function confirmFixAttachment() {
        if (!fixAttachmentTargetForm) return;

        const fileInput = document.getElementById('fixAttachmentInput');
        if (fileInput.files && fileInput.files[0]) {
            const clone = fileInput.cloneNode(true);
            clone.id = '';
            clone.name = 'fix_attachment';
            clone.classList.add('hidden');
            fixAttachmentTargetForm.appendChild(clone);
            fixAttachmentTargetForm.enctype = 'multipart/form-data';
        }

        document.getElementById('fixAttachmentModal').classList.add('hidden');
        document.getElementById('fixAttachmentModal').classList.remove('flex');
        fixAttachmentTargetForm.submit();
    }

</script>

<div id="fixAttachmentModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 backdrop-blur-sm p-4">
    <div class="w-full max-w-md p-6 rounded-2xl bg-[#131b2e] border border-slate-800 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h3 class="text-sm font-bold text-white">Tandai Selesai Diperbaiki</h3>
            <button type="button" onclick="closeFixAttachmentModal()" class="text-slate-400 hover:text-white text-lg font-bold cursor-pointer">&times;</button>
        </div>
        <div>
            <label class="block text-[11px] font-bold text-slate-400 mb-1">Upload File Bukti Perbaikan <span class="text-slate-600 font-normal normal-case">(Opsional)</span></label>
            <input type="file" id="fixAttachmentInput" accept="image/*" class="w-full text-xs text-slate-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-indigo-600 file:text-white file:text-xs file:font-semibold cursor-pointer">
            <p class="text-[10px] text-slate-600 mt-1">Format JPG/PNG/GIF/WEBP, maksimal 5MB.</p>
        </div>
        <div class="flex items-center justify-end gap-3 pt-2">
            <button type="button" onclick="closeFixAttachmentModal()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition cursor-pointer">Batal</button>
            <button type="button" onclick="confirmFixAttachment()" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold transition cursor-pointer">Tandai Selesai</button>
        </div>
    </div>
</div>

        </main>
    </div>

    <x-profile-modal />
</body>
</html>