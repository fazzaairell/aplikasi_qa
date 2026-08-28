<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Bug - QA Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        * { font-family: 'Inter', sans-serif; box-sizing: border-box; }
        body { background: #0c0f1a; }
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: #0c0f1a; }
        ::-webkit-scrollbar-thumb { background: rgba(99,102,241,0.3); border-radius: 99px; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
        .fade-in { animation: fadeIn 0.35s ease both; }
    </style>

</head>
<body class="h-full text-slate-100 flex overflow-hidden" x-data="{ sidebarOpen: false }">

{{-- SIDEBAR (Admin & QA) --}}
@if(auth()->user()->role !== 'Developer')
    <x-sidebar />
@endif

{{-- MAIN WRAPPER --}}
<div class="{{ auth()->user()->role !== 'Developer' ? 'flex-1 flex flex-col min-w-0 overflow-y-auto h-full' : 'w-full flex flex-col min-h-screen' }}">

    {{-- TOPBAR --}}
    @if(auth()->user()->role !== 'Developer')
    <header class="h-16 border-b border-white/[0.06] px-4 sm:px-8 flex items-center justify-between sticky top-0 z-30"
            style="background: rgba(12,15,26,0.85); backdrop-filter: blur(12px);">
        <div class="flex items-center gap-3">
            <button @click="$dispatch('toggle-sidebar')" class="md:hidden p-2 rounded-xl text-slate-400 hover:text-white border border-white/[0.06] cursor-pointer" style="background:#111827;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <a href="{{ url()->previous() }}" class="text-sm font-semibold text-slate-400 hover:text-indigo-400 transition flex items-center gap-2">
                &larr; Kembali
            </a>
        </div>
    </header>
    @else
    <header class="h-16 border-b sticky top-0 z-40" style="background:rgba(12,15,26,.85);backdrop-filter:blur(12px);border-color:rgba(255,255,255,.06);">
        <div class="max-w-7xl mx-auto px-8 h-full flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-teal-500 to-cyan-600 flex items-center justify-center font-bold text-white">Q</div>
                <span class="font-bold text-lg text-white">QA Platform</span>
            </div>
            <div class="hidden md:flex space-x-2">
                <a href="{{ route('dashboard.developer') }}" class="px-3 py-1.5 text-xs font-semibold text-slate-300 hover:text-white bg-white/[0.03] hover:bg-white/[0.08] rounded-lg transition border border-white/[0.05]">Dashboard</a>
                <a href="{{ route('notifications.timeline') }}" class="px-3 py-1.5 text-xs font-semibold text-slate-300 hover:text-white bg-white/[0.03] hover:bg-white/[0.08] rounded-lg transition border border-white/[0.05]">Notifikasi</a>
                <a href="{{ route('bugs.index') }}" class="px-3 py-1.5 text-xs font-semibold text-white bg-white/[0.1] rounded-lg transition border border-white/[0.1]">Bug Report</a>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ url()->previous() }}" class="text-sm font-semibold text-slate-400 hover:text-indigo-400 transition">&larr; Kembali</a>
                <form action="{{ route('logout') }}" method="POST">@csrf
                    <button type="submit" class="text-slate-400 hover:text-red-400 p-2 rounded-xl hover:bg-red-500/10 transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </header>
    @endif

    <main class="p-4 sm:p-6 lg:p-8 space-y-6 fade-in max-w-5xl mx-auto w-full">

        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
            <div>
                <div class="text-[10px] text-indigo-400 font-bold tracking-widest uppercase mb-1">DETAIL BUG</div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-tight leading-tight">{{ $bug->title }}</h1>
                <div class="flex items-center gap-4 mt-3 text-xs text-slate-400">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Dilaporkan: {{ $bug->created_at->format('d M Y, H:i') }}
                    </span>
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Oleh: {{ $bug->reporter->name ?? 'Unknown' }}
                    </span>
                </div>
            </div>
            <span class="px-3 py-1.5 rounded-xl text-xs font-bold border self-start"
                @if($bug->status === 'Open') style="background:rgba(239,68,68,0.1);color:#fca5a5;border-color:rgba(239,68,68,0.3);"
                @elseif($bug->status === 'In Progress') style="background:rgba(99,102,241,0.1);color:#a5b4fc;border-color:rgba(99,102,241,0.3);"
                @elseif(in_array($bug->status, ['Resolved','Closed','Done in Review'])) style="background:rgba(16,185,129,0.1);color:#6ee7b7;border-color:rgba(16,185,129,0.3);"
                @else style="background:rgba(168,85,247,0.1);color:#d8b4fe;border-color:rgba(168,85,247,0.3);" @endif>
                {{ $bug->status }}
            </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-[#111827] rounded-2xl border border-white/[0.06] p-6 shadow-xl">
                    <h3 class="text-sm font-bold text-white mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                        Deskripsi Masalah
                    </h3>
                    <div class="text-slate-300 text-sm leading-relaxed">{!! nl2br(e($bug->description)) !!}</div>
                </div>

                <div class="bg-[#111827] rounded-2xl border border-white/[0.06] p-6 shadow-xl">
                    <h3 class="text-sm font-bold text-white mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Expected Result
                    </h3>
                    <div class="text-slate-300 text-sm leading-relaxed">{!! nl2br(e($bug->expected_result ?? 'Tidak ada data expected result.')) !!}</div>
                </div>

                @if($bug->attachment_url)
                <div class="bg-[#111827] rounded-2xl border border-white/[0.06] p-6 shadow-xl">
                    <h3 class="text-sm font-bold text-white mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Lampiran / Screenshot
                    </h3>
                    <a href="{{ $bug->attachment_url }}" target="_blank" class="block border border-white/[0.06] rounded-xl overflow-hidden hover:opacity-90 transition">
                        <img src="{{ $bug->attachment_url }}" alt="Bug Attachment" class="w-full h-auto object-cover max-h-[400px]">
                    </a>
                </div>
                @endif
            </div>

            <div class="space-y-6">
                <div class="bg-[#111827] rounded-2xl border border-white/[0.06] p-6 shadow-xl space-y-5">
                    <div>
                        <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Project</div>
                        <div class="text-sm font-semibold text-white">{{ $bug->testResult?->testCase?->testSuite?->project?->name ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Test Suite</div>
                        <div class="text-sm font-semibold text-slate-300">{{ $bug->testResult?->testCase?->testSuite?->name ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Test Case</div>
                        <div class="text-sm font-semibold text-slate-300">{{ $bug->testResult?->testCase?->title ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Requirement</div>
                        <div class="text-sm font-semibold text-slate-300">{{ $bug->testResult?->testCase?->requirement?->title ?? '-' }}</div>
                    </div>
                    <hr class="border-white/[0.06]">
                    <div>
                        <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Priority</div>
                        @php $priority = $bug->testResult?->testCase?->priority ?? 'Low'; @endphp
                        @if($priority === 'Critical')<span class="px-2.5 py-1 rounded-lg text-xs font-bold" style="background:rgba(239,68,68,0.12);color:#fca5a5;border:1px solid rgba(239,68,68,0.25);">🔴 Critical</span>
                        @elseif($priority === 'High')<span class="px-2.5 py-1 rounded-lg text-xs font-bold" style="background:rgba(249,115,22,0.12);color:#fdba74;border:1px solid rgba(249,115,22,0.25);">🟠 High</span>
                        @elseif($priority === 'Medium')<span class="px-2.5 py-1 rounded-lg text-xs font-bold" style="background:rgba(234,179,8,0.12);color:#fde047;border:1px solid rgba(234,179,8,0.25);">🟡 Medium</span>
                        @else<span class="px-2.5 py-1 rounded-lg text-xs font-bold" style="background:rgba(100,116,139,0.12);color:#94a3b8;border:1px solid rgba(100,116,139,0.25);">⬜ Low</span>@endif
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Assigned To</div>
                        @if($bug->assignee)
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-lg flex items-center justify-center text-white text-[10px] font-bold" style="background: linear-gradient(135deg,#4f46e5,#7c3aed);">
                                    {{ strtoupper(substr($bug->assignee->name, 0, 2)) }}
                                </div>
                                <span class="text-sm font-semibold text-slate-300">{{ $bug->assignee->name }}</span>
                            </div>
                        @else<span class="text-sm text-slate-500 italic">Belum di-assign</span>@endif
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Due Date</div>
                        <div class="text-sm font-semibold text-slate-300">{{ $bug->due_date ? $bug->due_date->format('d M Y') : '-' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<x-profile-modal />
</body>
</html>
