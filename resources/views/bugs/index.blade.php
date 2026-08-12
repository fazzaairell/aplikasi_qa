<!DOCTYPE html>
<html lang="id" class="h-full bg-[#0b0f19]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bug Tracker - QA Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-full font-sans text-slate-100 flex overflow-hidden" 
      x-data="{ 
          sidebarOpen: false,
          collapsed: false
      }">

<x-sidebar />

    <!-- MAIN CONTENT -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto bg-[#0b0f19] h-full">
        
        <!-- TOPBAR -->
        <header class="h-20 border-b border-slate-800/80 px-8 flex items-center justify-between sticky top-0 bg-[#0b0f19]/80 backdrop-blur-md z-30">
            <div class="flex items-center space-x-4">
                <button @click="sidebarOpen = !sidebarOpen" class="md:hidden text-slate-400 hover:text-white p-2 rounded-lg bg-[#131b2e] border border-slate-800 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <div class="w-48 md:w-96">
                    <input type="text" placeholder="Cari bug, kendala..." class="w-full px-4 py-2 bg-[#131b2e] border border-slate-800 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500">
                </div>
            </div>
        </header>

        <main class="p-8 space-y-6">
            <!-- HEADER SECTION -->
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs text-red-800 font-semibold tracking-wider mb-1">PELACAKAN</div>
                    <h1 class="text-3xl font-bold text-white tracking-tight">Bug Tracker</h1>
                </div>
                <button class="px-4 py-2.5 rounded-lg bg-[#ff3b1f] hover:bg-red-600 text-white text-sm font-semibold transition cursor-pointer shadow-lg shadow-red-500/20">
                    + Laporan Bug
                </button>
            </div>

            <!-- STATISTICS CARDS -->
            <div class="grid grid-cols-5 gap-4">
                <div class="bg-[#131b2e] border border-red-500/20 rounded-xl p-4 space-y-2">
                    <div class="text-2xl font-bold text-white">{{ $bugs->where('status', 'Open')->count() }}</div>
                    <div class="text-xs text-slate-400">Open</div>
                </div>
                <div class="bg-[#131b2e] border border-blue-500/20 rounded-xl p-4 space-y-2">
                    <div class="text-2xl font-bold text-white">{{ $bugs->where('status', 'In Progress')->count() }}</div>
                    <div class="text-xs text-slate-400">In Progress</div>
                </div>
                <div class="bg-[#131b2e] border border-amber-500/20 rounded-xl p-4 space-y-2">
                    <div class="text-2xl font-bold text-white">{{ $bugs->where('status', 'Resolved')->count() }}</div>
                    <div class="text-xs text-slate-400">Resolved</div>
                </div>
                <div class="bg-[#131b2e] border border-purple-500/20 rounded-xl p-4 space-y-2">
                    <div class="text-2xl font-bold text-white">{{ $bugs->where('status', 'Reopened')->count() }}</div>
                    <div class="text-xs text-slate-400">Reopened</div>
                </div>
                <div class="bg-[#131b2e] border border-slate-500/20 rounded-xl p-4 space-y-2">
                    <div class="text-2xl font-bold text-white">{{ $bugs->where('status', 'Closed')->count() }}</div>
                    <div class="text-xs text-slate-400">Closed</div>
                </div>
            </div>

            <!-- FILTER BUTTONS -->
            <div class="flex items-center space-x-3">
                <button class="px-3.5 py-1.5 rounded-lg bg-slate-700/50 text-white text-xs font-medium transition cursor-pointer">All</button>
                <button class="px-3.5 py-1.5 rounded-lg bg-slate-800 text-slate-400 text-xs font-medium transition cursor-pointer hover:bg-slate-700">Open</button>
                <button class="px-3.5 py-1.5 rounded-lg bg-slate-800 text-slate-400 text-xs font-medium transition cursor-pointer hover:bg-slate-700">In Progress</button>
                <button class="px-3.5 py-1.5 rounded-lg bg-slate-800 text-slate-400 text-xs font-medium transition cursor-pointer hover:bg-slate-700">Resolved</button>
                <button class="px-3.5 py-1.5 rounded-lg bg-slate-800 text-slate-400 text-xs font-medium transition cursor-pointer hover:bg-slate-700">Closed</button>
                <button class="px-3.5 py-1.5 rounded-lg bg-slate-800 text-slate-400 text-xs font-medium transition cursor-pointer hover:bg-slate-700">Reopened</button>
            </div>

            <!-- BUGS TABLE -->
            <div class="bg-[#131b2e] border border-slate-800/80 rounded-2xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-xs min-w-max">
                        <thead>
                            <tr class="border-b border-slate-800/80 bg-[#0b0f19]/50 sticky top-0">
                                <th class="px-4 py-3 text-left text-slate-400 font-semibold whitespace-nowrap min-w-[50px]">ID</th>
                                <th class="px-4 py-3 text-left text-slate-400 font-semibold whitespace-nowrap min-w-[200px]">JUDUL BUG</th>
                                <th class="px-4 py-3 text-left text-slate-400 font-semibold whitespace-nowrap min-w-[150px]">PROJECT</th>
                                <th class="px-4 py-3 text-left text-slate-400 font-semibold whitespace-nowrap min-w-[180px]">REQUIREMENT</th>
                                <th class="px-4 py-3 text-left text-slate-400 font-semibold whitespace-nowrap min-w-[150px]">TEST SUITE</th>
                                <th class="px-4 py-3 text-left text-slate-400 font-semibold whitespace-nowrap min-w-[150px]">TEST CASE</th>
                                <th class="px-4 py-3 text-left text-slate-400 font-semibold whitespace-nowrap min-w-[100px]">DUE DATE</th>
                                <th class="px-4 py-3 text-left text-slate-400 font-semibold whitespace-nowrap min-w-[90px]">PRIORITY</th>
                                <th class="px-4 py-3 text-left text-slate-400 font-semibold whitespace-nowrap min-w-[100px]">STATUS</th>
                                <th class="px-4 py-3 text-left text-slate-400 font-semibold whitespace-nowrap min-w-[150px]">ASSIGNED TO</th>
                                <th class="px-4 py-3 text-left text-slate-400 font-semibold whitespace-nowrap min-w-[90px]">DIBUAT</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bugs as $bug)
                                <tr class="border-b border-slate-800/50 hover:bg-[#0b0f19]/50 transition">
                                    <td class="px-4 py-3 text-slate-400 whitespace-nowrap">{{ $bug->id }}</td>
                                    <td class="px-4 py-3">
                                        <div class="font-semibold text-white truncate max-w-[200px]">{{ $bug->title }}</div>
                                        <div class="text-slate-500 text-[10px] truncate max-w-[200px]">{{ $bug->description }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-slate-300 whitespace-nowrap">{{ $bug->testResult?->testCase?->testSuite?->project?->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-slate-300 whitespace-nowrap">{{ $bug->testResult?->testCase?->requirement?->title ?? '-' }}</td>
                                    <td class="px-4 py-3 text-slate-300 whitespace-nowrap">{{ $bug->testResult?->testCase?->testSuite?->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-slate-300 whitespace-nowrap">{{ $bug->testResult?->testCase?->title ?? '-' }}</td>
                                    <td class="px-4 py-3 text-slate-300 whitespace-nowrap">{{ $bug->testResult?->testCase?->requirement?->due_date ? $bug->testResult->testCase->requirement->due_date->format('Y-m-d') : '-' }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @php
                                            $priority = $bug->testResult?->testCase?->priority;
                                        @endphp
                                        @if($priority === 'Critical')
                                            <span class="px-2 py-1 rounded text-[9px] font-bold bg-red-500/20 text-red-400 border border-red-500/30">🔴 Critical</span>
                                        @elseif($priority === 'High')
                                            <span class="px-2 py-1 rounded text-[9px] font-bold bg-orange-500/20 text-orange-400 border border-orange-500/30">🟠 High</span>
                                        @elseif($priority === 'Medium')
                                            <span class="px-2 py-1 rounded text-[9px] font-bold bg-yellow-500/20 text-yellow-400 border border-yellow-500/30">🟡 Medium</span>
                                        @else
                                            <span class="px-2 py-1 rounded text-[9px] font-bold bg-slate-500/20 text-slate-400 border border-slate-500/30">⬜ Low</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap" @click="event.stopPropagation()">
                                        <form action="{{ route('bugs.update-status', $bug->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" onchange="this.form.submit()" class="
                                                px-2.5 py-1.5 rounded-lg text-[9px] font-bold border-2 cursor-pointer transition-all
                                                bg-[#0b0f19] text-white focus:outline-none
                                                {{ $bug->status === 'Open' ? 'bg-red-500/10 border-red-500/50 text-red-300 hover:border-red-500/80' : '' }}
                                                {{ $bug->status === 'In Progress' ? 'bg-blue-500/10 border-blue-500/50 text-blue-300 hover:border-blue-500/80' : '' }}
                                                {{ $bug->status === 'Resolved' ? 'bg-emerald-500/10 border-emerald-500/50 text-emerald-300 hover:border-emerald-500/80' : '' }}
                                                {{ $bug->status === 'Closed' ? 'bg-slate-500/10 border-slate-500/50 text-slate-300 hover:border-slate-500/80' : '' }}
                                                {{ $bug->status === 'Reopened' ? 'bg-purple-500/10 border-purple-500/50 text-purple-300 hover:border-purple-500/80' : '' }}
                                            ">
                                                <option value="Open" class="bg-[#131b2e] text-red-400" {{ $bug->status === 'Open' ? 'selected' : '' }}>Open</option>
                                                <option value="In Progress" class="bg-[#131b2e] text-blue-400" {{ $bug->status === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                                <option value="Resolved" class="bg-[#131b2e] text-emerald-400" {{ $bug->status === 'Resolved' ? 'selected' : '' }}>Resolved</option>
                                                <option value="Closed" class="bg-[#131b2e] text-slate-400" {{ $bug->status === 'Closed' ? 'selected' : '' }}>Closed</option>
                                                <option value="Reopened" class="bg-[#131b2e] text-purple-400" {{ $bug->status === 'Reopened' ? 'selected' : '' }}>Reopened</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @if($bug->assignee)
                                            <div class="flex items-center space-x-2">
                                                <div class="w-5 h-5 rounded-full bg-gradient-to-br from-orange-400 to-red-500 flex items-center justify-center text-white text-[8px] font-bold flex-shrink-0">
                                                    {{ substr($bug->assignee->name, 0, 1) }}
                                                </div>
                                                <span class="text-slate-300 truncate">{{ $bug->assignee->name }}</span>
                                            </div>
                                        @else
                                            <span class="text-slate-500">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-slate-500 whitespace-nowrap">{{ $bug->created_at->format('Y-m-d') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="px-4 py-16 text-center text-slate-500">
                                        Belum ada data bug yang tercatat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

</body>
</html>