<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bug History Report - QA Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        * { font-family: 'Inter', sans-serif; box-sizing: border-box; }
        body { background: #0c0f1a; }
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: #0c0f1a; }
        ::-webkit-scrollbar-thumb { background: rgba(99,102,241,0.3); border-radius: 99px; }
        @keyframes fadeIn { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }
        .fade-in { animation: fadeIn 0.4s ease both; }
    </style>
</head>
<body class="h-full text-slate-100 flex overflow-hidden" x-data="{ sidebarOpen: false }">

<x-sidebar />

<!-- MAIN CONTENT -->
<div class="flex-1 flex flex-col min-w-0 overflow-y-auto h-full">

    <div class="p-8 space-y-6 fade-in max-w-7xl mx-auto w-full">

        <div class="mb-8">
            <div class="flex items-center space-x-2 mb-1">
                <span class="w-2 h-2 rounded-full bg-rose-400 animate-pulse"></span>
                <span class="text-[11px] text-rose-400 font-bold tracking-widest uppercase">Bug History</span>
            </div>
            <h1 class="text-3xl font-bold text-white tracking-tight">Riwayat Bug Report</h1>
            <p class="text-sm text-slate-400 mt-1">Timeline perubahan status dan aktivitas semua bug dalam sistem.</p>
        </div>

        <!-- FILTER -->
        <div class="bg-[#111827] border border-slate-800/80 rounded-2xl p-5">
            <form method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Proyek</label>
                        <select name="project_id" class="w-full px-3 py-2.5 bg-[#0c0f1a] border border-indigo-500/20 rounded-xl text-xs text-white focus:outline-none focus:border-indigo-500">
                            <option value="">Semua Proyek</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                    {{ $project->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Status Bug</label>
                        <select name="bug_status" class="w-full px-3 py-2.5 bg-[#0c0f1a] border border-indigo-500/20 rounded-xl text-xs text-white focus:outline-none focus:border-indigo-500">
                            <option value="">Semua Status</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}" {{ request('bug_status') == $status ? 'selected' : '' }}>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Tipe Perubahan</label>
                        <select name="field_name" class="w-full px-3 py-2.5 bg-[#0c0f1a] border border-indigo-500/20 rounded-xl text-xs text-white focus:outline-none focus:border-indigo-500">
                            <option value="">Semua Tipe</option>
                            <option value="status" {{ request('field_name') == 'status' ? 'selected' : '' }}>Status</option>
                            <option value="assigned_to" {{ request('field_name') == 'assigned_to' ? 'selected' : '' }}>Assignee</option>
                            <option value="description" {{ request('field_name') == 'description' ? 'selected' : '' }}>Deskripsi</option>
                        </select>
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="w-full px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-semibold shadow-lg shadow-indigo-600/30 transition">
                            Terapkan Filter
                        </button>
                    </div>
                </div>

                @if(request()->filled(['project_id', 'bug_status', 'field_name']))
                    <div class="text-center">
                        <a href="{{ route('report.bug-history') }}" class="text-xs text-indigo-400 hover:text-indigo-300 font-semibold">
                            Reset Filter
                        </a>
                    </div>
                @endif
            </form>
        </div>

        <!-- TIMELINE -->
        <div class="space-y-4">
            @forelse($histories as $history)
                <div class="bg-[#111827] border border-slate-800/80 rounded-2xl p-5 hover:border-indigo-500/50 transition">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center font-bold text-xs border border-indigo-500/30 shrink-0">
                            @if($history->changedBy)
                                {{ strtoupper(substr($history->changedBy->name, 0, 2)) }}
                            @else
                                SY
                            @endif
                        </div>

                        <div class="flex-grow min-w-0">
                            <div class="flex items-center justify-between gap-2 mb-1.5">
                                <div>
                                    <h3 class="text-sm font-bold text-white">
                                        {{ $history->changedBy?->name ?? 'System' }}
                                    </h3>
                                    <p class="text-[11px] text-slate-500 mt-0.5">
                                        {{ $history->created_at->format('d M Y, H:i') }}
                                    </p>
                                </div>
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-slate-800/60 text-slate-300 whitespace-nowrap">
                                    {{ ucfirst($history->field_name) }}
                                </span>
                            </div>

                            <p class="text-slate-300 text-xs mb-3">
                                {{ $history->description }}
                            </p>

                            <div class="flex items-center gap-2 text-[11px] mb-3">
                                @if($history->old_value)
                                    <span class="px-2 py-1 rounded-lg font-semibold" style="background:rgba(239,68,68,0.1); color:#fca5a5; border:1px solid rgba(239,68,68,0.25);">
                                        {{ $history->old_value }}
                                    </span>
                                    <span class="text-slate-600">&rarr;</span>
                                @endif
                                @if($history->new_value)
                                    <span class="px-2 py-1 rounded-lg font-semibold" style="background:rgba(16,185,129,0.1); color:#6ee7b7; border:1px solid rgba(16,185,129,0.25);">
                                        {{ $history->new_value }}
                                    </span>
                                @endif
                            </div>

                            <a href="{{ route('report.bug-detail', $history->bug->id) }}" class="inline-flex items-center gap-1.5 text-xs text-indigo-400 hover:text-indigo-300 font-semibold">
                                {{ $history->bug->title }}
                                <span class="text-slate-500 font-normal">#{{ $history->bug->id }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-[#111827] border border-slate-800/80 rounded-2xl p-12 text-center">
                    <p class="text-slate-400 text-sm">Belum ada riwayat bug dengan filter yang dipilih.</p>
                </div>
            @endforelse

            @if($histories->hasPages())
                <div class="mt-6 flex justify-center">
                    {{ $histories->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<x-profile-modal />
</body>
</html>