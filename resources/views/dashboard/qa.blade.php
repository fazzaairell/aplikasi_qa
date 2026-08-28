<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard QA - QA Platform</title>
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
                <span class="w-2 h-2 rounded-full bg-indigo-400 animate-pulse"></span>
                <span class="text-[11px] text-indigo-400 font-bold tracking-widest uppercase">Quality Assurance Center</span>
            </div>
            <h1 class="text-3xl font-bold text-white tracking-tight">Ringkasan Status Pengujian</h1>
            <p class="text-sm text-slate-400 mt-1">Pantau progress test run dan kualitas rilis secara real-time.</p>
        </div>

        <!-- FILTER PROYEK -->
        <form method="GET" class="flex items-center space-x-2">
            <select name="project_id" onchange="this.form.submit()" class="px-4 py-2.5 bg-[#111827] border border-indigo-500/20 rounded-xl text-xs text-white focus:outline-none focus:border-indigo-500">
                <option value="">Semua Proyek</option>
                @foreach($projects as $p)
                    <option value="{{ $p->id }}" {{ request('project_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                @endforeach
            </select>
        </form>

        <!-- KARTU NAVIGASI CEPAT -->
        <div class="grid grid-cols-1 sm:grid-cols-5 gap-4">
            <a href="{{ route('requirements.index') }}" class="group bg-gradient-to-br from-[#111827] to-[#0c0f1a] border border-indigo-500/20 rounded-2xl p-5 relative overflow-hidden hover:border-indigo-500/50 hover:bg-[#1f2937] transition duration-300">
                <div class="w-10 h-10 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 mb-3 group-hover:scale-110 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                </div>
                <div class="text-3xl font-extrabold text-white">{{ $totalRequirements }}</div>
                <div class="text-xs font-semibold text-slate-300 mt-1">Requirements</div>
                <div class="text-[10px] text-indigo-400 mt-2 font-medium">Lihat RTM &rarr;</div>
            </a>

            <a href="{{ route('test-suites.index') }}" class="group bg-gradient-to-br from-[#111827] to-[#0c0f1a] border border-violet-500/20 rounded-2xl p-5 relative overflow-hidden hover:border-violet-500/50 hover:bg-[#1f2937] transition duration-300">
                <div class="w-10 h-10 rounded-xl bg-violet-500/10 border border-violet-500/20 flex items-center justify-center text-violet-400 mb-3 group-hover:scale-110 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                <div class="text-3xl font-extrabold text-white">{{ $totalTestSuites }}</div>
                <div class="text-xs font-semibold text-slate-300 mt-1">Test Suites</div>
                <div class="text-[10px] text-violet-400 mt-2 font-medium">Lihat test suites &rarr;</div>
            </a>

            <a href="{{ route('test-runs.index') }}" class="group bg-gradient-to-br from-[#111827] to-[#0c0f1a] border border-purple-500/20 rounded-2xl p-5 relative overflow-hidden hover:border-purple-500/50 hover:bg-[#1f2937] transition duration-300">
                <div class="w-10 h-10 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400 mb-3 group-hover:scale-110 transition">
                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                </div>
                <div class="text-3xl font-extrabold text-white">{{ $totalTestRuns }}</div>
                <div class="text-xs font-semibold text-slate-300 mt-1">Test Runs</div>
                <div class="text-[10px] text-purple-400 mt-2 font-medium">Lihat test runs &rarr;</div>
            </a>

            <a href="{{ route('report.bug-history') }}" class="group bg-gradient-to-br from-[#111827] to-[#0c0f1a] border border-rose-500/20 rounded-2xl p-5 relative overflow-hidden hover:border-rose-500/50 hover:bg-[#1f2937] transition duration-300">
                <div class="w-10 h-10 rounded-xl bg-rose-500/10 border border-rose-500/20 flex items-center justify-center text-rose-400 mb-3 group-hover:scale-110 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div class="text-3xl font-extrabold text-white">{{ $totalBugs }}</div>
                <div class="text-xs font-semibold text-slate-300 mt-1">Bugs</div>
                <div class="text-[10px] text-rose-400 mt-2 font-medium">Lihat riwayat bug &rarr;</div>
            </a>

            <a href="{{ route('projects.index') }}" class="group bg-gradient-to-br from-[#111827] to-[#0c0f1a] border border-purple-500/20 rounded-2xl p-5 relative overflow-hidden hover:border-purple-500/50 hover:bg-[#1f2937] transition duration-300">
                <div class="w-10 h-10 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400 mb-3 group-hover:scale-110 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                </div>
                <div class="text-3xl font-extrabold text-white">{{ $totalProjects }}</div>
                <div class="text-xs font-semibold text-slate-300 mt-1">Projects</div>
                <div class="text-[10px] text-purple-400 mt-2 font-medium">Lihat projects &rarr;</div>
            </a>
        </div>

        <!-- TEST RUN TERBARU -->
        <div class="bg-[#111827] border border-slate-800/80 rounded-2xl overflow-hidden">
            <div class="p-5 border-b border-slate-800/80 flex items-center justify-between">
                <span class="text-sm font-bold text-white">Test Run Terbaru</span>
                <a href="{{ route('test-runs.index') }}" class="text-xs text-indigo-400 hover:text-indigo-300 font-semibold">Lihat semua &rarr;</a>
            </div>
            <div class="divide-y divide-slate-800/60">
                @forelse($recentRuns as $run)
                    <a href="{{ route('test-runs.index') }}" class="p-4 flex items-center justify-between text-xs hover:bg-indigo-500/5 transition block">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-lg bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            </div>
                            <div>
                                <div class="font-semibold text-white">{{ $run->title }}</div>
                                <div class="text-slate-500">{{ $run->project->name ?? '-' }} &middot; {{ $run->created_at->format('d M Y') }}</div>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $run->status === 'Active' ? 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20' : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' }}">{{ $run->status }}</span>
                    </a>
                @empty
                    <div class="p-6 text-center text-slate-500 text-xs">Belum ada test run.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<x-profile-modal />
</body>
</html>