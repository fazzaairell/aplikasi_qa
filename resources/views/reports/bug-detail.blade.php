<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bug Detail - QA Management</title>
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
                <div class="max-w-6xl mx-auto px-6 py-6">
                    <div class="flex items-center gap-4 mb-4">
                        <a href="{{ route('report.bug-history') }}" class="text-indigo-400 hover:text-indigo-300">← Kembali ke Report</a>
                    </div>
                    <h1 class="text-3xl font-bold text-white mb-2">Bug: {{ $bug->title }}</h1>
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 bg-slate-800 text-sm text-slate-300 rounded-full">#{{ $bug->id }}</span>
                        <span class="px-3 py-1 
                            @if($bug->status === 'Closed') bg-green-900/20 text-green-300
                            @elseif($bug->status === 'Open') bg-red-900/20 text-red-300
                            @elseif($bug->status === 'In Progress') bg-yellow-900/20 text-yellow-300
                            @else bg-purple-900/20 text-purple-300
                            @endif
                            rounded-full text-sm">
                            {{ $bug->status }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="max-w-6xl mx-auto px-6 py-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Main Info -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Bug Details Card -->
                        <div class="bg-[#1a1f2e] border border-slate-800 rounded-2xl p-6">
                            <h2 class="text-lg font-semibold text-white mb-4">📋 Informasi Bug</h2>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="text-xs text-slate-400 font-medium">Deskripsi</label>
                                    <p class="text-slate-200 mt-1">{{ $bug->description }}</p>
                                </div>

                                <div>
                                    <label class="text-xs text-slate-400 font-medium">Expected Result</label>
                                    <p class="text-slate-200 mt-1">{{ $bug->expected_result ?? 'Tidak ada' }}</p>
                                </div>

                                @if($bug->attachment)
                                    <div>
                                        <label class="text-xs text-slate-400 font-medium">Bukti Attachment</label>
                                        <div class="mt-2">
                                            <a href="{{ $bug->attachment_url }}" target="_blank" class="text-indigo-400 hover:text-indigo-300 text-sm">
                                                📎 Lihat Attachment
                                            </a>
                                        </div>
                                    </div>
                                @endif

                                <div class="grid grid-cols-2 gap-4 pt-4 border-t border-slate-700">
                                    <div>
                                        <label class="text-xs text-slate-400 font-medium">Reporter</label>
                                        <p class="text-slate-200 mt-1">{{ $bug->reporter?->name ?? 'Unknown' }}</p>
                                    </div>
                                    <div>
                                        <label class="text-xs text-slate-400 font-medium">Assigned To</label>
                                        <p class="text-slate-200 mt-1">{{ $bug->assignee?->name ?? 'Unassigned' }}</p>
                                    </div>
                                    <div>
                                        <label class="text-xs text-slate-400 font-medium">Due Date</label>
                                        <p class="text-slate-200 mt-1">
                                            {{ $bug->due_date ? $bug->due_date->format('d M Y') : '-' }}
                                        </p>
                                    </div>
                                    <div>
                                        <label class="text-xs text-slate-400 font-medium">Finished Date</label>
                                        <p class="text-slate-200 mt-1">
                                            {{ $bug->finish_date ? $bug->finish_date->format('d M Y') : '-' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Test Case Info -->
                        @if($bug->testResult && $bug->testResult->testCase)
                            <div class="bg-[#1a1f2e] border border-slate-800 rounded-2xl p-6">
                                <h2 class="text-lg font-semibold text-white mb-4">🧪 Test Case Terkait</h2>
                                <div class="space-y-2">
                                    <p><span class="text-slate-400">Title:</span> <span class="text-white">{{ $bug->testResult->testCase->title }}</span></p>
                                    <p><span class="text-slate-400">Requirement:</span> <span class="text-white">{{ $bug->testResult->testCase->requirement?->description ?? '-' }}</span></p>
                                    <p><span class="text-slate-400">Test Suite:</span> <span class="text-white">{{ $bug->testResult->testCase->testSuite?->name ?? '-' }}</span></p>
                                    <p><span class="text-slate-400">Project:</span> <span class="text-white">{{ $bug->testResult->testCase->testSuite?->project?->name ?? '-' }}</span></p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Timeline Sidebar -->
                    <div class="lg:col-span-1">
                        <div class="bg-[#1a1f2e] border border-slate-800 rounded-2xl p-6 sticky top-6">
                            <h2 class="text-lg font-semibold text-white mb-6">⏰ Riwayat Aktivitas</h2>
                            
                            <div class="space-y-4">
                                @forelse($bug->histories as $history)
                                    <div class="pb-4 border-b border-slate-700 last:border-b-0">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="text-xs bg-slate-800 text-slate-300 px-2 py-1 rounded">
                                                {{ ucfirst($history->field_name) }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-400 mb-1">
                                            {{ $history->created_at->format('d M Y H:i') }}
                                        </p>
                                        <p class="text-xs text-slate-300">
                                            {{ $history->changedBy?->name ?? 'System' }}
                                        </p>
                                        @if($history->old_value && $history->new_value)
                                            <div class="flex items-center gap-1 mt-2 text-xs">
                                                <span class="px-1.5 py-0.5 bg-red-900/20 text-red-300 rounded">
                                                    {{ $history->old_value }}
                                                </span>
                                                <span class="text-slate-600">→</span>
                                                <span class="px-1.5 py-0.5 bg-green-900/20 text-green-300 rounded">
                                                    {{ $history->new_value }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                @empty
                                    <p class="text-xs text-slate-500">Belum ada riwayat aktivitas.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

