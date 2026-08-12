@extends('layouts.topbar')

@section('title', 'Dashboard Developer - QA Platform')

@section('hero')
    <div class="flex items-center space-x-2 mb-1">
        <span class="text-teal-400 font-mono text-xs">&lt;/&gt;</span>
        <span class="text-[11px] text-teal-400 font-bold tracking-widest uppercase"></span>
    </div>
    <h1 class="text-3xl font-bold text-white tracking-tight">Developer Workspace</h1>
    <p class="text-sm text-slate-400 mt-1">Ini daftar bug yang perlu kamu tangani hari ini.</p>
@endsection

@section('content')

    <!-- KARTU NAVIGASI CEPAT -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('projects.index') }}" class="group bg-[#131b2e] border border-slate-800/80 rounded-2xl p-5 relative overflow-hidden hover:border-teal-500/50 hover:bg-[#0f1c22] transition duration-300">
            <div class="w-10 h-10 rounded-xl bg-teal-500/10 border border-teal-500/20 flex items-center justify-center text-teal-400 mb-3 group-hover:scale-110 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
            </div>
            <div class="text-3xl font-extrabold text-white"></div>
            <div class="text-xs font-semibold text-slate-300 mt-1">Total Proyek</div>
        </a>

        <a href="{{ route('requirements.index') }}" class="group bg-[#131b2e] border border-slate-800/80 rounded-2xl p-5 relative overflow-hidden hover:border-cyan-500/50 hover:bg-[#0f1c22] transition duration-300">
            <div class="w-10 h-10 rounded-xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center text-cyan-400 mb-3 group-hover:scale-110 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
            </div>
            <div class="text-3xl font-extrabold text-white"></div>
            <div class="text-xs font-semibold text-slate-300 mt-1">Requirements</div>
        </a>

        <a href="{{ route('test-suites.index') }}" class="group bg-[#131b2e] border border-slate-800/80 rounded-2xl p-5 relative overflow-hidden hover:border-sky-500/50 hover:bg-[#0f1c22] transition duration-300">
            <div class="w-10 h-10 rounded-xl bg-sky-500/10 border border-sky-500/20 flex items-center justify-center text-sky-400 mb-3 group-hover:scale-110 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
            <div class="text-3xl font-extrabold text-white"></div>
            <div class="text-xs font-semibold text-slate-300 mt-1">Test Suites</div>
        </a>

        <a href="{{ route('bugs.index') }}" class="group bg-gradient-to-br from-rose-500/10 to-[#131b2e] border border-rose-500/30 rounded-2xl p-5 relative overflow-hidden hover:border-rose-500/60 transition duration-300">
            <div class="w-10 h-10 rounded-xl bg-rose-500/20 border border-rose-500/30 flex items-center justify-center text-rose-400 mb-3 group-hover:scale-110 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <div class="text-3xl font-extrabold text-white"></div>
            <div class="text-xs font-semibold text-rose-300 mt-1">Bug Aktif</div>
        </a>
    </div>

    <!-- DAFTAR BUG - TABEL LENGKAP -->
    <div class="bg-[#131b2e] border border-slate-800/80 rounded-2xl overflow-hidden">

        {{-- HEADER --}}
        <div class="p-5 border-b border-slate-800/80 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-teal-400"></span>
                <span class="text-sm font-bold text-white">Bug yang Ditugaskan ke Saya</span>
                <span class="text-[10px] text-slate-500">{{ $bugs->count() }} bugs</span>
            </div>

            <a href="{{ route('bugs.index') }}" class="text-xs text-teal-400 hover:text-teal-300 font-semibold">
                Lihat semua &rarr;
            </a>
        </div>

        {{-- TABLE --}}
        <div class="overflow-x-auto w-full">
            <table class="w-full min-w-[1300px]">
                <thead>
                    <tr class="border-b border-slate-800/80 bg-[#0b0f19]/40">
                        <th class="w-[70px] px-5 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500">ID</th>
                        <th class="min-w-[250px] px-5 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500">JUDUL BUG</th>
                        <th class="min-w-[180px] px-5 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500">PROJECT</th>
                        <th class="min-w-[180px] px-5 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500">TEST SUITE</th>
                        <th class="min-w-[220px] px-5 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500">TEST CASE</th>
                        <th class="w-[130px] px-5 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500">DUE DATE</th>
                        <th class="w-[120px] px-5 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500">PRIORITY</th>
                        <th class="w-[160px] px-5 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500">STATUS</th>
                        <th class="w-[110px] px-5 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500">DIBUAT</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($bugs as $bug)
                        <tr class="border-b border-slate-800/50 hover:bg-teal-500/[0.03] transition-colors">

                            <td class="px-5 py-4">
                                <span class="text-xs font-medium text-slate-600">#{{ $bug->id }}</span>
                            </td>

                            <td class="px-5 py-4">
                                <div class="max-w-[250px]">
                                    <div class="text-sm font-semibold text-slate-100 truncate">{{ $bug->title }}</div>
                                    <div class="mt-1 text-[11px] text-slate-500 truncate">{{ $bug->description }}</div>
                                </div>
                            </td>

                            <td class="px-5 py-4">
                                <span class="text-xs text-slate-300 whitespace-nowrap">
                                    {{ $bug->testResult?->testCase?->testSuite?->project?->name ?? '-' }}
                                </span>
                            </td>

                            <td class="px-5 py-4">
                                <span class="text-xs text-slate-400 whitespace-nowrap">
                                    {{ $bug->testResult?->testCase?->testSuite?->name ?? '-' }}
                                </span>
                            </td>

                            <td class="px-5 py-4">
                                <div class="max-w-[220px]">
                                    <span class="text-xs text-slate-400 truncate block">
                                        {{ $bug->testResult?->testCase?->title ?? '-' }}
                                    </span>
                                </div>
                            </td>

                                    <td class="px-5 py-4 whitespace-nowrap">
                                        @if($bug->due_date)
                                            @php
                                                $isOverdue = $bug->due_date->isPast() && !in_array($bug->status, ['Resolved', 'Closed', 'Done in Review']);
                                            @endphp
                                            <span class="text-xs {{ $isOverdue ? 'text-rose-400 font-semibold' : 'text-slate-300' }}">
                                                {{ $bug->due_date->format('Y-m-d') }}
                                            </span>
                                        @else
                                            <span class="text-xs text-slate-600">-</span>
                                        @endif
                                    </td>

                            <td class="px-5 py-4 whitespace-nowrap">
                                @php
                                    $priority = $bug->testResult?->testCase?->priority;
                                @endphp
                                @if($priority === 'Critical')
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-red-400">
                                        <span class="w-2 h-2 rounded-full bg-red-400"></span> Critical
                                    </span>
                                @elseif($priority === 'High')
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-orange-400">
                                        <span class="w-2 h-2 rounded-full bg-orange-400"></span> High
                                    </span>
                                @elseif($priority === 'Medium')
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-yellow-400">
                                        <span class="w-2 h-2 rounded-full bg-yellow-400"></span> Medium
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-400">
                                        <span class="w-2 h-2 rounded-full bg-slate-500"></span> Low
                                    </span>
                                @endif
                            </td>

                            <td class="px-5 py-4">
                                <form action="{{ route('bugs.update-status', $bug->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <select
                                        name="status"
                                        onchange="this.form.submit()"
                                        class="w-[145px] px-3 py-2 rounded-lg text-xs font-semibold border outline-none cursor-pointer transition bg-[#0b0f19]
                                            {{ $bug->status === 'in Progress' ? 'border-blue-500/30 text-blue-400 hover:border-blue-500/60' : '' }}
                                            {{ $bug->status === 'Done in Review' ? 'border-emerald-500/30 text-emerald-400 hover:border-emerald-500/60' : '' }}"
                                    >
                                        <option value="in Progress" class="bg-[#131b2e]" {{ $bug->status === 'in Progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="Done in Review" class="bg-[#131b2e]" {{ $bug->status === 'Done in Review' ? 'selected' : '' }}>Done in Review</option>
                                    </select>
                                </form>
                            </td>

                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="text-[11px] text-slate-600">{{ $bug->created_at->format('Y-m-d') }}</span>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-5 py-16 text-center text-slate-500">
                                Tidak ada bug yang ditugaskan ke kamu saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection