<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprehensive Reports - QA Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        * { font-family: 'Inter', sans-serif; }
        body { background: #0c0f1a; }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: #0c0f1a; }
        ::-webkit-scrollbar-thumb { background: rgba(99,102,241,0.3); border-radius: 99px; }
    </style>
</head>
<body class="text-slate-100">
    <div class="min-h-screen bg-gradient-to-br from-[#0f1419] via-[#1a1f2e] to-[#0f1419]">
        @include('layouts.topbar')
        
        <div class="flex-1">
            <!-- Header -->
            <div class="bg-[#1a1f2e] border-b border-slate-800">
                <div class="max-w-7xl mx-auto px-6 py-8">
                    <h1 class="text-3xl font-bold text-white mb-2">📊 Comprehensive Reports</h1>
                    <p class="text-slate-400">Analisis mendalam tentang status testing, bug, dan kualitas proyek</p>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="max-w-7xl mx-auto px-6 py-6">
                <form method="GET" class="inline-block">
                    <select name="project_id" onchange="this.form.submit()" class="px-4 py-2 bg-[#1a1f2e] border border-indigo-500/20 rounded-xl text-xs text-white focus:outline-none focus:border-indigo-500">
                        <option value="">📁 Semua Proyek</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}" {{ request('project_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            <!-- Content -->
            <div class="max-w-7xl mx-auto px-6 pb-12">
                <!-- Key Metrics -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                    <!-- Pass Rate -->
                    <div class="bg-[#1a1f2e] border border-slate-800 rounded-2xl p-6">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs text-slate-400 font-medium">Pass Rate</span>
                            <span class="w-8 h-8 rounded-lg bg-green-500/10 flex items-center justify-center text-green-400 text-sm">✅</span>
                        </div>
                        <div class="text-4xl font-extrabold text-green-400">{{ $passRate }}%</div>
                        <div class="text-xs text-slate-500 mt-2">{{ $resultsByStatus['Passed'] }} / {{ $totalResults }} passed</div>
                    </div>

                    <!-- Total Bugs -->
                    <div class="bg-[#1a1f2e] border border-slate-800 rounded-2xl p-6">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs text-slate-400 font-medium">Total Bugs</span>
                            <span class="w-8 h-8 rounded-lg bg-rose-500/10 flex items-center justify-center text-rose-400 text-sm">🐛</span>
                        </div>
                        <div class="text-4xl font-extrabold text-rose-400">{{ $totalBugs }}</div>
                        <div class="text-xs text-slate-500 mt-2">{{ $bugsByStatus['Open'] }} open, {{ $bugsByStatus['In Progress'] }} in progress</div>
                    </div>

                    <!-- Test Runs -->
                    <div class="bg-[#1a1f2e] border border-slate-800 rounded-2xl p-6">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs text-slate-400 font-medium">Test Runs</span>
                            <span class="w-8 h-8 rounded-lg bg-indigo-500/10 flex items-center justify-center text-indigo-400 text-sm">▶️</span>
                        </div>
                        <div class="text-4xl font-extrabold text-indigo-400">{{ $totalRuns }}</div>
                        <div class="text-xs text-slate-500 mt-2">{{ $activeRuns }} active, {{ $completedRuns }} completed</div>
                    </div>

                    <!-- Avg Bugs/Run -->
                    <div class="bg-[#1a1f2e] border border-slate-800 rounded-2xl p-6">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs text-slate-400 font-medium">Avg Bugs/Run</span>
                            <span class="w-8 h-8 rounded-lg bg-orange-500/10 flex items-center justify-center text-orange-400 text-sm">📈</span>
                        </div>
                        <div class="text-4xl font-extrabold text-orange-400">{{ $avgBugsPerRun }}</div>
                        <div class="text-xs text-slate-500 mt-2">Quality metric</div>
                    </div>
                </div>

                <!-- Bug Status Distribution -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <div class="bg-[#1a1f2e] border border-slate-800 rounded-2xl p-6">
                        <h2 class="text-lg font-semibold text-white mb-4">🔴 Bug Status Distribution</h2>
                        <div class="space-y-3">
                            @foreach($bugsByStatus as $status => $count)
                                @php
                                    $percentage = $totalBugs > 0 ? ($count / $totalBugs * 100) : 0;
                                    $color = match($status) {
                                        'Open' => '#ef4444',
                                        'In Progress' => '#f59e0b',
                                        'Done in Review' => '#8b5cf6',
                                        'Closed' => '#10b981',
                                        'Reopened' => '#ec4899',
                                        default => '#6366f1'
                                    };
                                @endphp
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-xs text-slate-300">{{ $status }}</span>
                                        <span class="text-xs font-semibold text-slate-400">{{ $count }}</span>
                                    </div>
                                    <div class="w-full bg-slate-800 rounded-full h-2">
                                        <div class="h-2 rounded-full" style="width: {{ $percentage }}%; background: {{ $color }};"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Test Result Status -->
                    <div class="bg-[#1a1f2e] border border-slate-800 rounded-2xl p-6">
                        <h2 class="text-lg font-semibold text-white mb-4">✅ Test Result Status</h2>
                        <div class="space-y-3">
                            @foreach($resultsByStatus as $status => $count)
                                @php
                                    $percentage = $totalResults > 0 ? ($count / $totalResults * 100) : 0;
                                    $color = match($status) {
                                        'Passed' => '#10b981',
                                        'Failed' => '#ef4444',
                                        'Blocked' => '#f59e0b',
                                        'Untested' => '#6366f1',
                                        default => '#6b7280'
                                    };
                                @endphp
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-xs text-slate-300">{{ $status }}</span>
                                        <span class="text-xs font-semibold text-slate-400">{{ $count }}</span>
                                    </div>
                                    <div class="w-full bg-slate-800 rounded-full h-2">
                                        <div class="h-2 rounded-full" style="width: {{ $percentage }}%; background: {{ $color }};"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Recent Items -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Recent Bugs -->
                    <div class="bg-[#1a1f2e] border border-slate-800 rounded-2xl overflow-hidden">
                        <div class="p-6 border-b border-slate-800">
                            <h2 class="text-lg font-semibold text-white">🐛 Recent Bugs</h2>
                        </div>
                        <div class="divide-y divide-slate-800/60">
                            @forelse($recentBugs as $bug)
                                @php
                                    $bugClass = match($bug->status) {
                                        'Open' => 'bg-red-900/20 text-red-300',
                                        'In Progress' => 'bg-yellow-900/20 text-yellow-300',
                                        'Closed' => 'bg-green-900/20 text-green-300',
                                        default => 'bg-slate-800 text-slate-300'
                                    };
                                @endphp
                                <a href="{{ route('report.bug-detail', $bug->id) }}" class="p-4 hover:bg-indigo-500/5 transition block">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="text-sm font-semibold text-white">{{ $bug->title }}</div>
                                            <div class="text-xs text-slate-500 mt-1">
                                                #{{ $bug->id }} • {{ $bug->created_at->format('d M Y') }}
                                            </div>
                                        </div>
                                        <span class="px-2 py-1 text-xs font-bold rounded {{ $bugClass }}">
                                            {{ $bug->status }}
                                        </span>
                                    </div>
                                </a>
                            @empty
                                <div class="p-6 text-center text-slate-500 text-xs">Belum ada bugs</div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Recent Test Runs -->
                    <div class="bg-[#1a1f2e] border border-slate-800 rounded-2xl overflow-hidden">
                        <div class="p-6 border-b border-slate-800">
                            <h2 class="text-lg font-semibold text-white">▶️ Recent Test Runs</h2>
                        </div>
                        <div class="divide-y divide-slate-800/60">
                            @forelse($recentRuns as $run)
                                @php
                                    $runClass = $run->status === 'Active' ? 'bg-indigo-900/20 text-indigo-300' : 'bg-green-900/20 text-green-300';
                                @endphp
                                <a href="{{ route('test-runs.index') }}" class="p-4 hover:bg-indigo-500/5 transition block">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="text-sm font-semibold text-white">{{ $run->title }}</div>
                                            <div class="text-xs text-slate-500 mt-1">
                                                {{ $run->project?->name ?? '-' }} • {{ $run->created_at->format('d M Y') }}
                                            </div>
                                        </div>
                                        <span class="px-2 py-1 text-xs font-bold rounded {{ $runClass }}">
                                            {{ $run->status }}
                                        </span>
                                    </div>
                                </a>
                            @empty
                                <div class="p-6 text-center text-slate-500 text-xs">Belum ada test runs</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
