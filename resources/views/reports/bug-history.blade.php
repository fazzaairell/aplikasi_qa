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
        * { font-family: 'Inter', sans-serif; }
        body { background: #0c0f1a; }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: #0c0f1a; }
        ::-webkit-scrollbar-thumb { background: rgba(99,102,241,0.3); border-radius: 99px; }
    </style>
</head>
<body class="text-slate-100">
    <div class="min-h-screen bg-gradient-to-br from-[#0f1419] via-[#1a1f2e] to-[#0f1419]">
        <!-- Sidebar (Topbar) -->
        @include('layouts.topbar')
        
        <!-- Main Content -->
        <div class="flex-1">
            <!-- Header -->
            <div class="bg-[#1a1f2e] border-b border-slate-800">
                <div class="max-w-7xl mx-auto px-6 py-8">
                    <h1 class="text-3xl font-bold text-white mb-2">📊 Riwayat Bug Report</h1>
                    <p class="text-slate-400">Timeline perubahan status dan aktivitas semua bug dalam sistem</p>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="max-w-7xl mx-auto px-6 py-6">
                <div class="bg-[#1a1f2e] border border-slate-800 rounded-2xl p-6 mb-8">
                    <form method="GET" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <!-- Project Filter -->
                            <div>
                                <label class="block text-xs font-medium text-slate-300 mb-2">Proyek</label>
                                <select name="project_id" class="w-full px-3 py-2 bg-[#0b0f19] border border-slate-800 rounded-lg text-xs text-white focus:outline-none focus:border-indigo-500">
                                    <option value="">Semua Proyek</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                            {{ $project->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Status Filter -->
                            <div>
                                <label class="block text-xs font-medium text-slate-300 mb-2">Status Bug</label>
                                <select name="bug_status" class="w-full px-3 py-2 bg-[#0b0f19] border border-slate-800 rounded-lg text-xs text-white focus:outline-none focus:border-indigo-500">
                                    <option value="">Semua Status</option>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status }}" {{ request('bug_status') == $status ? 'selected' : '' }}>
                                            {{ $status }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Field Filter -->
                            <div>
                                <label class="block text-xs font-medium text-slate-300 mb-2">Tipe Perubahan</label>
                                <select name="field_name" class="w-full px-3 py-2 bg-[#0b0f19] border border-slate-800 rounded-lg text-xs text-white focus:outline-none focus:border-indigo-500">
                                    <option value="">Semua Tipe</option>
                                    <option value="status" {{ request('field_name') == 'status' ? 'selected' : '' }}>Status</option>
                                    <option value="assigned_to" {{ request('field_name') == 'assigned_to' ? 'selected' : '' }}>Assignee</option>
                                    <option value="description" {{ request('field_name') == 'description' ? 'selected' : '' }}>Deskripsi</option>
                                </select>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex items-end">
                                <button type="submit" class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-medium transition">
                                    🔍 Filter
                                </button>
                            </div>
                        </div>

                        @if(request()->filled(['project_id', 'bug_status', 'assigned_to', 'field_name']))
                            <div class="text-center">
                                <a href="{{ route('report.bug-history') }}" class="text-xs text-indigo-400 hover:text-indigo-300">
                                    ✕ Reset Filter
                                </a>
                            </div>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Timeline Section -->
            <div class="max-w-7xl mx-auto px-6 pb-12">
                @forelse($histories as $history)
                    <div class="mb-6 relative">
                        <!-- Timeline connector -->
                        @if(!$loop->last)
                            <div class="absolute left-6 top-20 w-0.5 h-16 bg-gradient-to-b from-indigo-500 to-slate-800"></div>
                        @endif

                        <!-- Timeline item -->
                        <div class="bg-[#1a1f2e] border border-slate-800 rounded-xl p-6 hover:border-indigo-500 transition">
                            <div class="flex items-start gap-4">
                                <!-- Avatar/Icon -->
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm">
                                        @if($history->changedBy)
                                            {{ strtoupper(substr($history->changedBy->name, 0, 1)) }}
                                        @else
                                            S
                                        @endif
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="flex-grow">
                                    <div class="flex items-center justify-between mb-2">
                                        <div>
                                            <h3 class="text-white font-semibold">
                                                @if($history->changedBy)
                                                    {{ $history->changedBy->name }}
                                                @else
                                                    System
                                                @endif
                                            </h3>
                                            <p class="text-xs text-slate-400 mt-1">
                                                {{ $history->created_at->format('d M Y H:i') }}
                                            </p>
                                        </div>
                                        <span class="inline-block px-2 py-1 bg-slate-800 text-xs text-slate-300 rounded">
                                            {{ ucfirst($history->field_name) }}
                                        </span>
                                    </div>

                                    <!-- Change Details -->
                                    <p class="text-slate-300 text-sm mb-3">
                                        {{ $history->description }}
                                    </p>

                                    <!-- Value changes -->
                                    <div class="flex items-center gap-2 text-xs mb-3">
                                        @if($history->old_value)
                                            <span class="px-2 py-1 bg-red-900/20 text-red-300 rounded border border-red-800">
                                                {{ $history->old_value }}
                                            </span>
                                            <span class="text-slate-500">→</span>
                                        @endif
                                        @if($history->new_value)
                                            <span class="px-2 py-1 bg-green-900/20 text-green-300 rounded border border-green-800">
                                                {{ $history->new_value }}
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Bug Info Link -->
                                    <div class="text-xs">
                                        <a href="{{ route('report.bug-detail', $history->bug->id) }}" class="text-indigo-400 hover:text-indigo-300 font-medium">
                                            📋 {{ $history->bug->title }}
                                            <span class="text-slate-500">(#{{ $history->bug->id }})</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-[#1a1f2e] border border-slate-800 rounded-xl p-12 text-center">
                        <p class="text-slate-400">Belum ada riwayat bug dengan filter yang dipilih.</p>
                    </div>
                @endforelse

                <!-- Pagination -->
                @if($histories->hasPages())
                    <div class="mt-8 flex justify-center">
                        {{ $histories->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>

