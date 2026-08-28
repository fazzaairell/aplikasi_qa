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
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: #0c0f1a; }
        ::-webkit-scrollbar-thumb { background: rgba(99,102,241,0.3); border-radius: 99px; }
        .filter-scroll { overflow-x: auto; -ms-overflow-style: none; scrollbar-width: none; }
        .filter-scroll::-webkit-scrollbar { display: none; }
        .filter-btn { white-space: nowrap; }
        .filter-btn.active { background: rgba(99,102,241,0.15); color: #818cf8; border-color: rgba(99,102,241,0.35); }
        select.status-open     { border-color: rgba(239,68,68,0.5);   color: #fca5a5; }
        select.status-progress { border-color: rgba(99,102,241,0.5);  color: #a5b4fc; }
        select.status-resolved { border-color: rgba(16,185,129,0.5);  color: #6ee7b7; }
        select.status-closed   { border-color: rgba(100,116,139,0.5); color: #94a3b8; }
        select.status-reopened { border-color: rgba(168,85,247,0.5);  color: #d8b4fe; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
        .fade-in { animation: fadeIn 0.35s ease both; }
        tr.bug-row:hover td { background: rgba(99,102,241,0.04); }
    </style>

</head>
<body class="h-full text-slate-100 flex overflow-hidden" x-data="{ sidebarOpen: false, showLaporanModal: {{ $errors->any() || session('success') ? 'true' : 'false' }} }">

{{-- ═══ SIDEBAR (Admin & QA Tester) ═══ --}}
@if(auth()->user()->role !== 'Developer')
    <x-sidebar />
@endif

{{-- ═══ MAIN WRAPPER ═══ --}}
<div class="{{ auth()->user()->role !== 'Developer' ? 'flex-1 flex flex-col min-w-0 overflow-y-auto h-full' : 'w-full flex flex-col min-h-screen' }}">

    {{-- ─── TOPBAR ─────────────────────────────────────────────────────────── --}}
    @if(auth()->user()->role !== 'Developer')
    {{-- Admin / QA Tester: simple topbar dengan search --}}
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
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input type="text" placeholder="Cari bug..."
                       id="bug-search"
                       class="w-48 sm:w-64 md:w-80 pl-9 pr-3 py-2 rounded-xl text-xs text-white placeholder-slate-500 border border-white/[0.06] focus:outline-none focus:border-indigo-500/50 transition"
                       style="background:#111827;"
                       oninput="filterBugs()">
            </div>
        </div>
    </header>
    @else
    {{-- Developer: topbar penuh dengan nav link di tengah --}}
    <header class="h-16 border-b sticky top-0 z-40" style="background:rgba(12,15,26,.85);backdrop-filter:blur(12px);border-color:rgba(255,255,255,.06);">
        <div class="max-w-7xl mx-auto px-8 h-full flex items-center justify-between relative">
            <div class="flex items-center space-x-3 w-1/3">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-teal-500 to-cyan-600 flex items-center justify-center font-bold text-white shadow-lg shadow-indigo-500/30">Q</div>
                <span class="font-bold text-lg text-white tracking-wide">QA Platform</span>
            </div>
            <div class="hidden md:flex flex-1 justify-center space-x-2 w-1/3">
                <a href="{{ route('dashboard.developer') }}" class="px-3 py-1.5 text-xs font-semibold text-slate-300 hover:text-white bg-white/[0.03] hover:bg-white/[0.08] rounded-lg transition border border-white/[0.05]">Dashboard</a>
                <a href="{{ route('notifications.timeline') }}" class="px-3 py-1.5 text-xs font-semibold text-slate-300 hover:text-white bg-white/[0.03] hover:bg-white/[0.08] rounded-lg transition border border-white/[0.05]">Notifikasi</a>
                <a href="{{ route('bugs.index') }}" class="px-3 py-1.5 text-xs font-semibold text-white bg-white/[0.1] rounded-lg transition border border-white/[0.1]">Bug Report</a>
            </div>
            <div class="flex items-center justify-end space-x-3 w-1/3">
                @php
                    $unreadCount = \App\Models\BugNotification::where('user_id', auth()->id())->where('is_read', false)->count();
                    $recentNotifs = \App\Models\BugNotification::where('user_id', auth()->id())->with('bug')->latest()->take(8)->get();
                @endphp
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button @click="open = !open" type="button" class="relative p-2 rounded-xl text-slate-400 hover:text-white hover:bg-white/5 transition cursor-pointer" title="Notifikasi">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        @if($unreadCount > 0)
                        <span class="absolute -top-0.5 -right-0.5 w-4 h-4 rounded-full text-[9px] font-bold text-white flex items-center justify-center" style="background:#4f46e5;">
                            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                        </span>
                        @endif
                    </button>
                    <div x-show="open" x-cloak style="display:none;" class="absolute right-0 mt-2 w-80 rounded-2xl border shadow-2xl z-50 overflow-hidden" style="background:#111827; border-color:rgba(255,255,255,0.07);">
                        <div class="px-4 py-3 border-b flex items-center justify-between" style="border-color:rgba(255,255,255,0.06);">
                            <span class="text-sm font-bold text-white">Notifikasi</span>
                            @if($unreadCount > 0)
                            <form action="{{ route('notifications.read-all') }}" method="POST">@csrf
                                <button type="submit" class="text-[10px] text-indigo-400 hover:text-indigo-300 font-semibold cursor-pointer">Tandai semua dibaca</button>
                            </form>
                            @endif
                        </div>
                        <div class="max-h-72 overflow-y-auto">
                            @if($recentNotifs->isEmpty())
                            <div class="px-4 py-8 text-center text-slate-500 text-xs">Belum ada notifikasi</div>
                            @else
                            @foreach($recentNotifs as $notif)
                            <a href="{{ route('notifications.read', $notif->id) }}" class="flex items-start gap-3 px-4 py-3 hover:bg-white/[0.03] transition border-b" style="border-color:rgba(255,255,255,0.04);">
                                <span class="mt-1 w-2 h-2 rounded-full shrink-0 {{ $notif->is_read ? 'bg-slate-700' : 'bg-indigo-500' }}"></span>
                                <div class="min-w-0">
                                    <p class="text-xs {{ $notif->is_read ? 'text-slate-500' : 'text-slate-200' }} leading-snug">{{ $notif->message }}</p>
                                    <p class="text-[10px] text-slate-600 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                                </div>
                            </a>
                            @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                <button type="button" @click="$dispatch('open-profile-modal')" class="flex items-center space-x-3 cursor-pointer text-left" title="Buka profil">
                    <div class="w-9 h-9 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center font-bold text-xs border border-indigo-500/30 overflow-hidden">
                        @if(auth()->user()->photo_path)
                            <img src="{{ asset('uploads/' . auth()->user()->photo_path) }}" class="w-full h-full object-cover" alt="Foto profil">
                        @else
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        @endif
                    </div>
                    <div class="hidden sm:block">
                        <div class="text-xs font-bold text-white">{{ auth()->user()->name }}</div>
                        <div class="text-[10px] text-indigo-400 font-semibold">{{ auth()->user()->role }}</div>
                    </div>
                </button>
                <form action="{{ route('logout') }}" method="POST">@csrf
                    <button type="submit" class="text-slate-400 hover:text-red-400 p-2 rounded-xl hover:bg-red-500/10 transition cursor-pointer" title="Keluar">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </header>
    @endif

    {{-- ─── MAIN CONTENT ────────────────────────────────────────────────────── --}}
    <main class="p-4 sm:p-6 lg:p-8 space-y-5 fade-in {{ auth()->user()->role === 'Developer' ? 'max-w-7xl mx-auto w-full' : '' }}">

        {{-- PAGE HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <div class="text-[10px] text-indigo-400 font-bold tracking-widest uppercase mb-1">PELACAKAN</div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">
                    {{ $isHistory ? 'Riwayat Bug' : 'Bug Tracker' }}
                </h1>
            </div>
            <div class="flex items-center gap-3">
                @if($isHistory)
                    <a href="{{ route('bugs.index') }}" class="px-4 py-2.5 rounded-xl text-white text-sm font-semibold border border-white/[0.06] hover:bg-slate-800 transition" style="background:#111827;">
                        ← Bugs Aktif
                    </a>
                @else
                    <a href="{{ route('bugs.history') }}" class="px-4 py-2.5 rounded-xl text-white text-sm font-semibold border border-white/[0.06] hover:bg-slate-800 transition" style="background:#111827;">
                        Riwayat Bug
                    </a>
                @endif

                @if(auth()->user()->role !== 'Developer')
                <button id="btn-laporan-bug"
                    @click="showLaporanModal = true"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-white text-sm font-semibold transition shadow-lg cursor-pointer"
                    style="background: linear-gradient(135deg,#4f46e5,#6366f1); box-shadow: 0 8px 20px rgba(79,70,229,0.25);"
                    onmouseover="this.style.transform='translateY(-1px)'" onmouseout="this.style.transform=''">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Laporan Bug
                </button>
                @endif
            </div>
        </div>

        {{-- STATISTICS CARDS --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
            @php
                $countOpen     = $bugs->where('status', 'Open')->count();
                $countProgress = $bugs->where('status', 'In Progress')->count();
                $countResolved = $bugs->where('status', 'Resolved')->count();
                $countReopened = $bugs->where('status', 'Reopened')->count();
                $countClosed   = $bugs->where('status', 'Closed')->count();
            @endphp
            <div class="rounded-2xl p-4 space-y-1 hover:scale-105 transition duration-200" style="background:rgba(239,68,68,0.15); border:1px solid rgba(239,68,68,0.25);">
                <div class="text-2xl font-bold text-white">{{ $countOpen }}</div>
                <div class="text-xs font-semibold" style="color:#fca5a5">Open</div>
            </div>
            <div class="rounded-2xl p-4 space-y-1 hover:scale-105 transition duration-200" style="background:rgba(99,102,241,0.15); border:1px solid rgba(99,102,241,0.25);">
                <div class="text-2xl font-bold text-white">{{ $countProgress }}</div>
                <div class="text-xs font-semibold" style="color:#a5b4fc">In Progress</div>
            </div>
            <div class="rounded-2xl p-4 space-y-1 hover:scale-105 transition duration-200" style="background:rgba(16,185,129,0.15); border:1px solid rgba(16,185,129,0.25);">
                <div class="text-2xl font-bold text-white">{{ $countResolved }}</div>
                <div class="text-xs font-semibold" style="color:#6ee7b7">Resolved</div>
            </div>
            <div class="rounded-2xl p-4 space-y-1 hover:scale-105 transition duration-200" style="background:rgba(168,85,247,0.15); border:1px solid rgba(168,85,247,0.25);">
                <div class="text-2xl font-bold text-white">{{ $countReopened }}</div>
                <div class="text-xs font-semibold" style="color:#d8b4fe">Reopened</div>
            </div>
            <div class="rounded-2xl p-4 space-y-1 hover:scale-105 transition duration-200" style="background:rgba(100,116,139,0.15); border:1px solid rgba(100,116,139,0.25);">
                <div class="text-2xl font-bold text-white">{{ $countClosed }}</div>
                <div class="text-xs font-semibold" style="color:#94a3b8">Closed</div>
            </div>
        </div>

        {{-- FORM FILTER --}}
        <form method="GET" action="{{ $isHistory ? route('bugs.history') : route('bugs.index') }}"
              class="flex flex-wrap items-center gap-3 p-4 rounded-2xl border border-white/[0.06] shadow-lg" style="background:#111827;">
            <div class="flex items-center gap-2">
                <label class="text-xs font-semibold text-slate-400">Status:</label>
                <select name="status" class="bg-[#0c0f1a] border border-white/[0.1] rounded-xl text-xs text-white px-3 py-1.5 focus:outline-none focus:border-indigo-500">
                    <option value="All" {{ request('status') == 'All' || !request('status') ? 'selected' : '' }}>Semua</option>
                    <option value="Open" {{ request('status') == 'Open' ? 'selected' : '' }}>Open</option>
                    <option value="In Progress" {{ request('status') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="Done in Review" {{ request('status') == 'Done in Review' ? 'selected' : '' }}>Done in Review</option>
                    <option value="Resolved" {{ request('status') == 'Resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="Closed" {{ request('status') == 'Closed' ? 'selected' : '' }}>Closed</option>
                    <option value="Reopened" {{ request('status') == 'Reopened' ? 'selected' : '' }}>Reopened</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <label class="text-xs font-semibold text-slate-400">Project:</label>
                <select name="project_id" class="bg-[#0c0f1a] border border-white/[0.1] rounded-xl text-xs text-white px-3 py-1.5 focus:outline-none focus:border-indigo-500">
                    <option value="">Semua Project</option>
                    @foreach($projects as $proj)
                        <option value="{{ $proj->id }}" {{ request('project_id') == $proj->id ? 'selected' : '' }}>{{ $proj->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-2">
                <label class="text-xs font-semibold text-slate-400">Dari:</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="bg-[#0c0f1a] border border-white/[0.1] rounded-xl text-xs text-slate-300 px-3 py-1.5 focus:outline-none focus:border-indigo-500">
            </div>
            <div class="flex items-center gap-2">
                <label class="text-xs font-semibold text-slate-400">Sampai:</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="bg-[#0c0f1a] border border-white/[0.1] rounded-xl text-xs text-slate-300 px-3 py-1.5 focus:outline-none focus:border-indigo-500">
            </div>
            <button type="submit" class="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-xl transition shadow-lg shadow-indigo-600/30">Terapkan Filter</button>
            <a href="{{ $isHistory ? route('bugs.history') : route('bugs.index') }}" class="px-4 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-xl transition">Reset</a>
        </form>

        {{-- BUGS TABLE --}}
        <div class="rounded-2xl border border-white/[0.06] overflow-hidden" style="background:#111827;">
            <div class="overflow-x-auto">
                <table class="w-full text-xs" style="min-width: 900px;" id="bugs-table">
                    <thead>
                        <tr class="border-b border-white/[0.06]" style="background:rgba(12,15,26,0.6);">
                            <th class="px-4 py-3.5 text-left text-slate-400 font-semibold uppercase tracking-wider whitespace-nowrap w-12">No</th>
                            <th class="px-4 py-3.5 text-left text-slate-400 font-semibold uppercase tracking-wider whitespace-nowrap min-w-[200px]">Judul Bug</th>
                            <th class="px-4 py-3.5 text-left text-slate-400 font-semibold uppercase tracking-wider whitespace-nowrap min-w-[130px]">Project</th>
                            <th class="px-4 py-3.5 text-left text-slate-400 font-semibold uppercase tracking-wider whitespace-nowrap min-w-[100px]">Due Date</th>
                            <th class="px-4 py-3.5 text-left text-slate-400 font-semibold uppercase tracking-wider whitespace-nowrap min-w-[100px]">Finish Date</th>
                            <th class="px-4 py-3.5 text-left text-slate-400 font-semibold uppercase tracking-wider whitespace-nowrap min-w-[85px]">Priority</th>
                            <th class="px-4 py-3.5 text-left text-slate-400 font-semibold uppercase tracking-wider whitespace-nowrap min-w-[110px]">Status</th>
                            <th class="px-4 py-3.5 text-left text-slate-400 font-semibold uppercase tracking-wider whitespace-nowrap min-w-[140px]">Assigned To</th>
                            <th class="px-4 py-3.5 text-left text-slate-400 font-semibold uppercase tracking-wider whitespace-nowrap min-w-[90px]">Dibuat</th>
                            <th class="px-4 py-3.5 text-left text-slate-400 font-semibold uppercase tracking-wider whitespace-nowrap min-w-[80px]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="bugs-tbody">
                        @if($bugs->isEmpty())
                        <tr>
                            <td colspan="10" class="px-4 py-20 text-center">
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
                            $dueDate     = $bug->due_date;
                            $priority    = $bug->testResult?->testCase?->priority ?? 'Low';
                        @endphp
                        <tr class="bug-row border-b border-white/[0.04] transition" data-status="{{ $bug->status }}">
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="text-slate-500 font-mono font-semibold">{{ $rowNum }}</span>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-semibold text-white leading-snug truncate max-w-[200px]" title="{{ $bug->title }}">{{ $bug->title }}</div>
                                @if($bug->description)
                                <div class="text-slate-500 text-[10px] truncate max-w-[200px] mt-0.5">{{ $bug->description }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                @if($projectName)<span class="text-slate-300">{{ $projectName }}</span>
                                @else<span class="text-slate-600">—</span>@endif
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                @if($dueDate)<span class="text-slate-400 font-mono text-[11px]">{{ $dueDate->format('d M Y') }}</span>
                                @else<span class="text-slate-600">—</span>@endif
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                @if($bug->finish_date)
                                <span class="px-2 py-1 rounded-lg text-[10px] font-semibold" style="background:rgba(16,185,129,0.1); color:#6ee7b7; border:1px solid rgba(16,185,129,0.25);">
                                    {{ $bug->finish_date->format('d M Y') }}
                                </span>
                                @else<span class="text-slate-600">—</span>@endif
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                @if($priority === 'Critical')<span class="px-2 py-1 rounded-lg text-[9px] font-bold" style="background:rgba(239,68,68,0.12);color:#fca5a5;border:1px solid rgba(239,68,68,0.25);">🔴 Critical</span>
                                @elseif($priority === 'High')<span class="px-2 py-1 rounded-lg text-[9px] font-bold" style="background:rgba(249,115,22,0.12);color:#fdba74;border:1px solid rgba(249,115,22,0.25);">🟠 High</span>
                                @elseif($priority === 'Medium')<span class="px-2 py-1 rounded-lg text-[9px] font-bold" style="background:rgba(234,179,8,0.12);color:#fde047;border:1px solid rgba(234,179,8,0.25);">🟡 Medium</span>
                                @else<span class="px-2 py-1 rounded-lg text-[9px] font-bold" style="background:rgba(100,116,139,0.12);color:#94a3b8;border:1px solid rgba(100,116,139,0.25);">⬜ Low</span>@endif
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap" onclick="event.stopPropagation()">
                                @if($isHistory)
                                    <span class="px-3 py-1.5 rounded-lg text-[10px] font-bold"
                                        @if($bug->status === 'Open') style="background:rgba(239,68,68,0.1);color:#fca5a5;border:1px solid rgba(239,68,68,0.3);"
                                        @elseif($bug->status === 'In Progress') style="background:rgba(99,102,241,0.1);color:#a5b4fc;border:1px solid rgba(99,102,241,0.3);"
                                        @elseif(in_array($bug->status, ['Resolved','Closed','Done in Review'])) style="background:rgba(16,185,129,0.1);color:#6ee7b7;border:1px solid rgba(16,185,129,0.3);"
                                        @else style="background:rgba(168,85,247,0.1);color:#d8b4fe;border:1px solid rgba(168,85,247,0.3);" @endif>
                                        {{ $bug->status }}
                                    </span>
                                @else
                                <form action="{{ route('bugs.update-status', $bug->id) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <select name="status" onchange="this.form.submit()"
                                            class="px-2.5 py-1.5 rounded-lg text-[10px] font-bold cursor-pointer outline-none border-2 transition-all"
                                            class="bg-[#0c0f1a]"
                                            @if(auth()->user()->role === 'Developer' && in_array($bug->status, ['Resolved','Closed','Reopened'])) disabled @endif>
                                        @if(auth()->user()->role === 'Developer')
                                            <option value="Open" {{ $bug->status === 'Open' ? 'selected' : '' }} disabled>Open</option>
                                            <option value="In Progress" {{ $bug->status === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                            <option value="Done in Review" {{ $bug->status === 'Done in Review' ? 'selected' : '' }}>Done in Review</option>
                                            @if(in_array($bug->status, ['Resolved','Closed','Reopened']))
                                                <option value="{{ $bug->status }}" selected>{{ $bug->status }}</option>
                                            @endif
                                        @else
                                            <option value="Open" {{ $bug->status === 'Open' ? 'selected' : '' }}>Open</option>
                                            <option value="In Progress" {{ $bug->status === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                            <option value="Done in Review" {{ $bug->status === 'Done in Review' ? 'selected' : '' }}>Done in Review</option>
                                            <option value="Resolved" {{ $bug->status === 'Resolved' ? 'selected' : '' }}>Resolved</option>
                                            <option value="Closed" {{ $bug->status === 'Closed' ? 'selected' : '' }}>Closed</option>
                                            <option value="Reopened" {{ $bug->status === 'Reopened' ? 'selected' : '' }}>Reopened</option>
                                        @endif
                                    </select>
                                </form>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                @if($bug->assignee)
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-lg flex items-center justify-center text-white text-[9px] font-bold shrink-0" style="background: linear-gradient(135deg,#4f46e5,#7c3aed);">
                                        {{ strtoupper(substr($bug->assignee->name, 0, 2)) }}
                                    </div>
                                    <span class="text-slate-300 truncate max-w-[100px]">{{ $bug->assignee->name }}</span>
                                </div>
                                @else<span class="text-slate-600">—</span>@endif
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="text-slate-500 font-mono text-[10px]">{{ $bug->created_at->format('d M Y') }}</span>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap text-right">
                                <a href="{{ route('bugs.show', $bug->id) }}" class="px-3 py-1.5 bg-[#0b0f19] border border-slate-700/80 hover:bg-indigo-600/20 text-indigo-400 hover:text-indigo-300 text-[10px] font-bold rounded-lg transition">Detail</a>
                            </td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="px-4 py-3 border-t border-white/[0.04] flex items-center justify-between">
                <span class="text-[11px] text-slate-500" id="row-count">Menampilkan {{ $bugs->count() }} bug</span>
                <span class="text-[11px] text-slate-600">Total: {{ $bugs->count() }} data</span>
            </div>
        </div>

    </main>
</div>

<x-profile-modal />

{{-- ═══ MODAL LAPORAN BUG ═══ --}}
@if(auth()->user()->role !== 'Developer')
<div x-show="showLaporanModal"
     x-cloak
     class="fixed inset-0 z-[80] flex items-center justify-center bg-black/70 backdrop-blur-sm"
     style="display:none;"
     @keydown.escape.window="showLaporanModal = false">
    <div @click.away="showLaporanModal = false"
         class="bg-[#111827] border border-white/[0.08] rounded-2xl w-full max-w-lg shadow-2xl mx-4 max-h-[92vh] overflow-y-auto">

        {{-- Modal Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-white/[0.06]">
            <div>
                <h3 class="text-sm font-bold text-white">Laporan Bug Baru</h3>
                <p class="text-[11px] text-slate-500 mt-0.5">Laporkan bug yang ditemukan langsung ke Bug Tracker</p>
            </div>
            <button @click="showLaporanModal = false"
                    type="button"
                    class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-400 hover:text-white hover:bg-white/5 transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Form --}}
        <form action="{{ route('bugs.store') }}" method="POST" enctype="multipart/form-data"
              x-data="{ previewUrl: null }" class="px-6 py-5 space-y-4">
            @csrf

            {{-- Flash success --}}
            @if(session('success'))
            <div class="px-4 py-3 rounded-xl text-xs font-semibold text-emerald-300" style="background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.25);">
                ✅ {{ session('success') }}
            </div>
            @endif

            {{-- Judul Bug --}}
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Judul Bug <span class="text-rose-400">*</span></label>
                <input type="text" name="title" required value="{{ old('title') }}"
                       placeholder="Contoh: Tombol submit tidak merespons"
                       class="w-full px-3 py-2.5 bg-[#0c0f1a] border border-white/[0.1] rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition">
                @error('title')<span class="text-xs text-rose-400">{{ $message }}</span>@enderror
            </div>

            {{-- Deskripsi --}}
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Deskripsi Bug <span class="text-rose-400">*</span></label>
                <textarea name="description" required rows="3"
                          placeholder="Jelaskan langkah-langkah reproduksi bug..."
                          class="w-full px-3 py-2.5 bg-[#0c0f1a] border border-white/[0.1] rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition resize-none">{{ old('description') }}</textarea>
                @error('description')<span class="text-xs text-rose-400">{{ $message }}</span>@enderror
            </div>

            {{-- Expected Result --}}
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Expected Result</label>
                <textarea name="expected_result" rows="2"
                          placeholder="Apa yang seharusnya terjadi?"
                          class="w-full px-3 py-2.5 bg-[#0c0f1a] border border-white/[0.1] rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition resize-none">{{ old('expected_result') }}</textarea>
            </div>

            {{-- Assign + Due Date --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Assign ke Developer <span class="text-rose-400">*</span></label>
                    <select name="assigned_to" required
                            class="w-full px-3 py-2.5 bg-[#0c0f1a] border border-white/[0.1] rounded-xl text-xs text-white focus:outline-none focus:border-indigo-500 transition">
                        <option value="">-- Pilih Developer --</option>
                        @foreach($developers as $dev)
                            <option value="{{ $dev->id }}" {{ old('assigned_to') == $dev->id ? 'selected' : '' }}>{{ $dev->name }}</option>
                        @endforeach
                    </select>
                    @error('assigned_to')<span class="text-xs text-rose-400">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Due Date <span class="text-rose-400">*</span></label>
                    <input type="date" name="due_date" required value="{{ old('due_date') }}"
                           class="w-full px-3 py-2.5 bg-[#0c0f1a] border border-white/[0.1] rounded-xl text-xs text-slate-300 focus:outline-none focus:border-indigo-500 transition">
                    @error('due_date')<span class="text-xs text-rose-400">{{ $message }}</span>@enderror
                </div>
            </div>

            {{-- Upload Screenshot --}}
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Screenshot / Lampiran</label>
                <div class="relative">
                    {{-- Drop zone --}}
                    <label x-show="!previewUrl"
                           class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-white/[0.12] rounded-xl cursor-pointer hover:border-indigo-500/50 hover:bg-indigo-500/5 transition">
                        <div class="flex flex-col items-center gap-2 text-slate-500">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-[11px] font-medium">Klik untuk upload gambar</span>
                            <span class="text-[10px] text-slate-600">JPG, PNG, GIF, WEBP • Maks. 5MB</span>
                        </div>
                        <input type="file" name="attachment" accept="image/*" class="hidden"
                               @change="
                                   const file = $event.target.files[0];
                                   if (file) previewUrl = URL.createObjectURL(file);
                               ">
                    </label>

                    {{-- Preview gambar --}}
                    <div x-show="previewUrl" class="relative rounded-xl overflow-hidden border border-white/[0.08]">
                        <img :src="previewUrl" alt="Preview" class="w-full h-40 object-cover">
                        <button type="button"
                                @click="previewUrl = null; $el.closest('form').querySelector('input[type=file]').value = '';"
                                class="absolute top-2 right-2 w-7 h-7 rounded-full bg-rose-600 hover:bg-rose-500 text-white flex items-center justify-center text-sm transition cursor-pointer shadow-lg">
                            &times;
                        </button>
                        <div class="absolute bottom-0 inset-x-0 px-3 py-1.5 text-[10px] text-white" style="background:rgba(0,0,0,0.5)">
                            <span x-text="previewUrl ? 'Gambar siap diupload' : ''"></span>
                        </div>
                    </div>
                </div>
                @error('attachment')<span class="text-xs text-rose-400">{{ $message }}</span>@enderror
            </div>

            {{-- Actions --}}
            <div class="flex justify-end gap-2 pt-2 border-t border-white/[0.06]">
                <button type="button" @click="showLaporanModal = false"
                        class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs text-slate-300 cursor-pointer transition">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-xl text-xs text-white font-semibold transition cursor-pointer flex items-center gap-1.5"
                        style="background:linear-gradient(135deg,#dc2626,#b91c1c);box-shadow:0 4px 12px rgba(220,38,38,0.3);">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Laporkan Bug
                </button>
            </div>
        </form>
    </div>
</div>
@endif


<script>
    function colorStatusSelects() {
        document.querySelectorAll('select[name="status"]').forEach(sel => {
            sel.classList.remove('status-open','status-progress','status-resolved','status-closed','status-reopened');
            const v = sel.value;
            if      (v === 'Open')         sel.classList.add('status-open');
            else if (v === 'In Progress')  sel.classList.add('status-progress');
            else if (v === 'Resolved')     sel.classList.add('status-resolved');
            else if (v === 'Closed')       sel.classList.add('status-closed');
            else if (v === 'Reopened')     sel.classList.add('status-reopened');
        });
    }

    function filterBugs() {
        const search = (document.getElementById('bug-search')?.value || '').toLowerCase().trim();
        const rows = document.querySelectorAll('.bug-row');
        let visible = 0;
        rows.forEach(row => {
            const match = search === '' || row.innerText.toLowerCase().includes(search);
            row.style.display = match ? '' : 'none';
            if (match) visible++;
        });
        let no = 1;
        rows.forEach(row => {
            if (row.style.display !== 'none') {
                const cell = row.querySelector('td:first-child span');
                if (cell) cell.textContent = no++;
            }
        });
        const cnt = document.getElementById('row-count');
        if (cnt) cnt.textContent = 'Menampilkan ' + visible + ' bug';
    }

    document.addEventListener('DOMContentLoaded', () => {
        colorStatusSelects();
        document.querySelectorAll('select[name="status"]').forEach(sel => {
            sel.addEventListener('change', () => colorStatusSelects());
        });
    });
</script>

</body>
</html>
