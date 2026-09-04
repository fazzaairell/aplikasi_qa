<!DOCTYPE html>

<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


<title>{{ $project->name }} - Detail Proyek - QA Management</title>

<script src="https://cdn.tailwindcss.com"></script>

<link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
    rel="stylesheet"
>

<script
    defer
    src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"
></script>

<style>
    * {
        font-family: 'Inter', sans-serif;
    }

    body {
        background: #0b0f19;
    }

    ::-webkit-scrollbar {
        width: 5px;
        height: 5px;
    }

    ::-webkit-scrollbar-track {
        background: #0b0f19;
    }

    ::-webkit-scrollbar-thumb {
        background: rgba(99,102,241,.3);
        border-radius: 99px;
    }

    @keyframes fadeUp {
        from {
            opacity: 0;
            transform: translateY(18px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .fade-up {
        animation: fadeUp .45s ease both;
    }

    .card {
        background: #111827;
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 1rem;
    }

    .plan-card {
        background: rgba(99,102,241,0.05);
        border: 1px solid rgba(99,102,241,0.15);
        border-radius: .875rem;
        transition: border-color .2s, background .2s;
    }

    .plan-card:hover {
        background: rgba(99,102,241,0.09);
        border-color: rgba(99,102,241,0.3);
    }

    .stat-card {
        background: #111827;
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: .875rem;
        transition: transform .2s, border-color .2s;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        border-color: rgba(99,102,241,0.3);
    }

    .input-field {
        width: 100%;
        padding: .625rem 1rem;
        background: #0b0f19;
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: .75rem;
        color: #f1f5f9;
        font-size: .75rem;
        outline: none;
        transition: border-color .2s;
    }

    .input-field:focus {
        border-color: #6366f1;
    }

    .lbl {
        display: block;
        font-size: .6875rem;
        font-weight: 700;
        color: #94a3b8;
        margin-bottom: .25rem;
    }
</style>


</head>

<body
    class="h-full font-sans text-slate-100 flex overflow-hidden"
    x-data="{ showEditForm: false }"
>


<x-sidebar />

<div class="flex-1 flex flex-col min-w-0 overflow-y-auto h-full">

    <header
        class="h-16 border-b px-4 sm:px-8 flex items-center justify-between sticky top-0 z-30"
        style="background:rgba(11,15,25,.9);backdrop-filter:blur(14px);border-color:rgba(255,255,255,.06);"
    >
        <a
            href="{{ route('projects.index') }}"
            class="inline-flex items-center gap-2 text-xs font-semibold text-slate-400 hover:text-indigo-400 transition"
        >
            <svg
                class="w-4 h-4"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M15 19l-7-7 7-7"
                />
            </svg>

            Kembali ke Daftar Proyek
        </a>

        <span class="text-xs text-slate-500 font-medium hidden sm:block">
            {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
        </span>
    </header>


    <main class="p-6 sm:p-8 space-y-7 max-w-7xl mx-auto w-full">

        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-semibold flex items-center gap-2">
                <svg
                    class="w-4 h-4 shrink-0"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M5 13l4 4L19 7"
                    />
                </svg>

                {{ session('success') }}
            </div>
        @endif


        {{-- HERO --}}
        <div
            class="fade-up card p-7 relative overflow-hidden"
            style="animation-delay:0s"
        >
            <div
                class="absolute -top-16 -right-16 w-72 h-72 rounded-full pointer-events-none"
                style="background:radial-gradient(circle,rgba(99,102,241,.12),transparent 70%);"
            ></div>

            <div class="relative flex flex-col md:flex-row md:items-start justify-between gap-6">

                <div class="flex-1 min-w-0">

                    <div class="flex items-center gap-1.5 mb-3">
                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 animate-pulse"></span>

                        <span class="text-[10px] font-bold tracking-[.15em] uppercase text-indigo-400">
                            Detail Proyek
                        </span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-tight leading-tight mb-3">
                        {{ $project->name }}
                    </h1>

                    <p class="text-sm text-slate-400 leading-relaxed max-w-2xl">
                        {{ $project->description ?: 'Tidak ada deskripsi.' }}
                    </p>

                    <div class="flex items-center gap-3 mt-4">

                        @php
                            $sc = match($project->status ?? 'Aktif') {
                                'Aktif' => [
                                    'bg-emerald-500/10 border-emerald-500/25 text-emerald-400',
                                    'bg-emerald-400'
                                ],

                                'Selesai' => [
                                    'bg-indigo-500/10 border-indigo-500/25 text-indigo-400',
                                    'bg-indigo-400'
                                ],

                                'Pending' => [
                                    'bg-amber-500/10 border-amber-500/25 text-amber-400',
                                    'bg-amber-400'
                                ],

                                default => [
                                    'bg-slate-700/30 border-slate-700 text-slate-400',
                                    'bg-slate-400'
                                ],
                            };
                        @endphp

                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border text-[11px] font-bold {{ $sc[0] }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $sc[1] }}"></span>

                            {{ $project->status ?? 'Aktif' }}
                        </span>

                        <span class="text-[11px] text-slate-500">
                            Dibuat {{ $project->created_at->format('d M Y') }}
                        </span>

                    </div>
                </div>


                <div class="flex items-center gap-3 shrink-0">

                    <button
                        @click="showEditForm = !showEditForm"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold transition cursor-pointer"
                        style="background:rgba(245,158,11,.12);border:1px solid rgba(245,158,11,.25);color:#fbbf24;"
                    >
                        <svg
                            class="w-3.5 h-3.5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                            />
                        </svg>

                        Edit Proyek
                    </button>


                    <form
                        action="{{ route('projects.destroy', $project->id) }}"
                        method="POST"
                        onsubmit="return confirm('Hapus proyek ini?');"
                    >
                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold transition cursor-pointer"
                            style="background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.25);color:#f87171;"
                        >
                            <svg
                                class="w-3.5 h-3.5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                />
                            </svg>

                            Hapus
                        </button>
                    </form>

                </div>
            </div>
        </div>


        {{-- STATISTIK --}}
        <div
            class="fade-up grid grid-cols-2 sm:grid-cols-4 gap-4"
            style="animation-delay:.08s"
        >

            @php
                $totalSuites = $project->testSuites->count();
                $totalCases = $project->testSuites->flatMap->testCases->count();
                $totalReqs = $project->requirements->count();
                $totalRuns = $project->testRuns->count();

                $stats = [
                    [
                        'Test Suites',
                        $totalSuites,
                        'text-indigo-400',
                        'rgba(99,102,241,.2)'
                    ],
                    [
                        'Test Cases',
                        $totalCases,
                        'text-violet-400',
                        'rgba(139,92,246,.2)'
                    ],
                    [
                        'Requirements',
                        $totalReqs,
                        'text-emerald-400',
                        'rgba(16,185,129,.2)'
                    ],
                    [
                        'Test Runs',
                        $totalRuns,
                        'text-amber-400',
                        'rgba(245,158,11,.2)'
                    ],
                ];
            @endphp


            @foreach($stats as [$label, $val, $color, $border])

                <div
                    class="stat-card p-5"
                    x-bind:style="'border-color: {{ $border }};'"
                >
                    <div class="text-2xl font-bold text-white mb-1">
                        {{ $val }}
                    </div>

                    <div class="text-xs font-semibold {{ $color }}">
                        {{ $label }}
                    </div>
                </div>

            @endforeach

        </div>


        {{-- TEST PLAN --}}
        @php
            $tp = is_array($project->test_plan)
                ? $project->test_plan
                : [];
        @endphp


        <div
            class="fade-up"
            style="animation-delay:.14s"
        >

            <div class="card p-6">

                <div class="flex items-center gap-3 mb-5">

                    <div
                        class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0"
                        style="background:rgba(99,102,241,.15);border:1px solid rgba(99,102,241,.25);"
                    >
                        <svg
                            class="w-4 h-4 text-indigo-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                            />
                        </svg>
                    </div>

                    <div>
                        <h2 class="text-sm font-bold text-white">
                            Test Plan
                        </h2>

                        <p class="text-[11px] text-slate-500">
                            Rencana pengujian proyek ini
                        </p>
                    </div>

                </div>


                @if(count(array_filter($tp)) > 0)

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

                        @if(!empty($tp['scope']))

                            <div class="plan-card p-5">

                                <div class="flex items-center gap-2 mb-2">

                                    <div
                                        class="w-6 h-6 rounded-lg flex items-center justify-center"
                                        style="background:rgba(99,102,241,.2);"
                                    >
                                        <svg
                                            class="w-3.5 h-3.5 text-indigo-400"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                            />
                                        </svg>
                                    </div>

                                    <span class="text-[10px] font-bold uppercase tracking-widest text-indigo-300">
                                        Scope
                                    </span>

                                </div>

                                <p class="text-xs text-slate-300 leading-relaxed">
                                    {{ $tp['scope'] }}
                                </p>

                            </div>

                        @endif


                        @if(!empty($tp['objective']))

                            <div class="plan-card p-5">

                                <div class="flex items-center gap-2 mb-2">

                                    <div
                                        class="w-6 h-6 rounded-lg flex items-center justify-center"
                                        style="background:rgba(139,92,246,.2);"
                                    >
                                        <svg
                                            class="w-3.5 h-3.5 text-violet-400"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M13 10V3L4 14h7v7l9-11h-7z"
                                            />
                                        </svg>
                                    </div>

                                    <span class="text-[10px] font-bold uppercase tracking-widest text-violet-300">
                                        Objective
                                    </span>

                                </div>

                                <p class="text-xs text-slate-300 leading-relaxed">
                                    {{ $tp['objective'] }}
                                </p>

                            </div>

                        @endif


                        @if(!empty($tp['resource']))

                            <div class="plan-card p-5">

                                <div class="flex items-center gap-2 mb-2">

                                    <div
                                        class="w-6 h-6 rounded-lg flex items-center justify-center"
                                        style="background:rgba(16,185,129,.2);"
                                    >
                                        <svg
                                            class="w-3.5 h-3.5 text-emerald-400"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"
                                            />
                                        </svg>
                                    </div>

                                    <span class="text-[10px] font-bold uppercase tracking-widest text-emerald-300">
                                        Resource
                                    </span>

                                </div>

                                <p class="text-xs text-slate-300 leading-relaxed">
                                    {{ $tp['resource'] }}
                                </p>

                            </div>

                        @endif


                        @if(!empty($tp['schedule']))

                            <div class="plan-card p-5">

                                <div class="flex items-center gap-2 mb-2">

                                    <div
                                        class="w-6 h-6 rounded-lg flex items-center justify-center"
                                        style="background:rgba(245,158,11,.2);"
                                    >
                                        <svg
                                            class="w-3.5 h-3.5 text-amber-400"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                            />
                                        </svg>
                                    </div>

                                    <span class="text-[10px] font-bold uppercase tracking-widest text-amber-300">
                                        Schedule
                                    </span>

                                </div>

                                <p class="text-xs text-slate-300 leading-relaxed">
                                    {{ $tp['schedule'] }}
                                </p>

                            </div>

                        @endif


                        @if(!empty($tp['risk']))

                            <div class="plan-card p-5">

                                <div class="flex items-center gap-2 mb-2">

                                    <div
                                        class="w-6 h-6 rounded-lg flex items-center justify-center"
                                        style="background:rgba(239,68,68,.2);"
                                    >
                                        <svg
                                            class="w-3.5 h-3.5 text-red-400"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                                            />
                                        </svg>
                                    </div>

                                    <span class="text-[10px] font-bold uppercase tracking-widest text-red-300">
                                        Risk
                                    </span>

                                </div>

                                <p class="text-xs text-slate-300 leading-relaxed">
                                    {{ $tp['risk'] }}
                                </p>

                            </div>

                        @endif

                    </div>

                @else

                    <div class="flex flex-col items-center justify-center py-10 gap-3">

                        <div
                            class="w-12 h-12 rounded-2xl flex items-center justify-center"
                            style="background:rgba(99,102,241,.08);border:1px solid rgba(99,102,241,.15);"
                        >
                            <svg
                                class="w-6 h-6 text-indigo-400/60"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.5"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                                />
                            </svg>
                        </div>

                        <p class="text-xs text-slate-500">
                            Belum ada test plan.

                            <button
                                @click="showEditForm=true"
                                class="text-indigo-400 hover:underline cursor-pointer"
                            >
                                Tambahkan sekarang
                            </button>
                        </p>

                    </div>

                @endif

            </div>
        </div>


        {{-- QUICK LINKS --}}
        <div
            class="fade-up grid grid-cols-1 sm:grid-cols-3 gap-4"
            style="animation-delay:.2s"
        >

            <a
                href="{{ route('requirements.index', ['project_id' => $project->id]) }}"
                class="card p-5 flex items-center gap-4 hover:border-white/[0.12] transition group"
            >
                <div
                    class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 transition-transform group-hover:scale-110"
                    style="background:rgba(99,102,241,.1);border:1px solid rgba(99,102,241,.2);"
                >
                    <svg
                        class="w-5 h-5 text-indigo-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"
                        />
                    </svg>
                </div>

                <div>
                    <div class="text-sm font-bold text-white group-hover:text-indigo-300 transition">
                        Requirements
                    </div>

                    <div class="text-[11px] text-slate-500">
                        Kelola kebutuhan sistem
                    </div>
                </div>

                <svg
                    class="w-4 h-4 text-slate-600 ml-auto group-hover:text-indigo-400 transition"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 5l7 7-7 7"
                    />
                </svg>
            </a>


            <a
                href="{{ route('test-suites.index', ['project_id' => $project->id]) }}"
                class="card p-5 flex items-center gap-4 hover:border-white/[0.12] transition group"
            >
                <div
                    class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 transition-transform group-hover:scale-110"
                    style="background:rgba(139,92,246,.1);border:1px solid rgba(139,92,246,.2);"
                >
                    <svg
                        class="w-5 h-5 text-violet-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"
                        />
                    </svg>
                </div>

                <div>
                    <div class="text-sm font-bold text-white group-hover:text-violet-300 transition">
                        Test Suites
                    </div>

                    <div class="text-[11px] text-slate-500">
                        Kelola suite &amp; test case
                    </div>
                </div>

                <svg
                    class="w-4 h-4 text-slate-600 ml-auto group-hover:text-violet-400 transition"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 5l7 7-7 7"
                    />
                </svg>
            </a>


            <a
                href="{{ route('test-runs.index', ['project_id' => $project->id]) }}"
                class="card p-5 flex items-center gap-4 hover:border-white/[0.12] transition group"
            >
                <div
                    class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 transition-transform group-hover:scale-110"
                    style="background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.2);"
                >
                    <svg
                        class="w-5 h-5 text-emerald-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"
                        />

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                </div>

                <div>
                    <div class="text-sm font-bold text-white group-hover:text-emerald-300 transition">
                        Test Runs
                    </div>

                    <div class="text-[11px] text-slate-500">
                        Lihat eksekusi pengujian
                    </div>
                </div>

                <svg
                    class="w-4 h-4 text-slate-600 ml-auto group-hover:text-emerald-400 transition"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 5l7 7-7 7"
                    />
                </svg>
            </a>

        </div>


        {{-- FORM EDIT --}}
        <div
            x-show="showEditForm"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-3"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-3"
            x-cloak
            style="display:none;"
            class="card p-6"
        >

            <div class="flex items-center justify-between mb-5">

                <h2 class="text-sm font-bold text-white flex items-center gap-2">

                    <svg
                        class="w-4 h-4 text-amber-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                        />
                    </svg>

                    Edit Proyek

                </h2>


                <button
                    @click="showEditForm=false"
                    class="text-slate-500 hover:text-white transition cursor-pointer"
                >
                    <svg
                        class="w-5 h-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>

            </div>


            <form
                action="{{ route('projects.update', $project->id) }}"
                method="POST"
                class="space-y-5"
            >
                @csrf
                @method('PUT')


                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <div>
                        <label class="lbl">
                            Nama Proyek
                        </label>

                        <input
                            type="text"
                            name="name"
                            value="{{ $project->name }}"
                            required
                            class="input-field"
                        >
                    </div>


                    <div>
                        <label class="lbl">
                            Status
                        </label>

                        <select
                            name="status"
                            class="input-field"
                        >
                            <option
                                value="Aktif"
                                {{ $project->status == 'Aktif' ? 'selected' : '' }}
                            >
                                Aktif
                            </option>

                            <option
                                value="Pending"
                                {{ $project->status == 'Pending' ? 'selected' : '' }}
                            >
                                Pending
                            </option>

                            <option
                                value="Selesai"
                                {{ $project->status == 'Selesai' ? 'selected' : '' }}
                            >
                                Selesai
                            </option>
                        </select>
                    </div>

                </div>


                <div>
                    <label class="lbl">
                        Deskripsi
                    </label>

                    <textarea
                        name="description"
                        rows="3"
                        class="input-field resize-none"
                    >{{ $project->description }}</textarea>
                </div>


                <div class="pt-2">

                    <div class="flex items-center gap-2 mb-4">

                        <div
                            class="h-px flex-1"
                            style="background:rgba(255,255,255,0.06);"
                        ></div>

                        <span class="text-[10px] font-bold text-indigo-400 uppercase tracking-widest px-2">
                            Detail Test Plan
                        </span>

                        <div
                            class="h-px flex-1"
                            style="background:rgba(255,255,255,0.06);"
                        ></div>

                    </div>


                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        <div>
                            <label class="lbl">
                                Scope
                            </label>

                            <textarea
                                name="test_plan[scope]"
                                rows="3"
                                placeholder="Ruang lingkup pengujian..."
                                class="input-field resize-none"
                            >{{ $tp['scope'] ?? '' }}</textarea>
                        </div>


                        <div>
                            <label class="lbl">
                                Objective
                            </label>

                            <textarea
                                name="test_plan[objective]"
                                rows="3"
                                placeholder="Tujuan pengujian..."
                                class="input-field resize-none"
                            >{{ $tp['objective'] ?? '' }}</textarea>
                        </div>


                        <div>
                            <label class="lbl">
                                Resource
                            </label>

                            <input
                                type="text"
                                name="test_plan[resource]"
                                value="{{ $tp['resource'] ?? '' }}"
                                placeholder="Contoh: 3 QA Engineer, 1 Automation"
                                class="input-field"
                            >
                        </div>


                        <div>
                            <label class="lbl">
                                Schedule
                            </label>

                            <input
                                type="text"
                                name="test_plan[schedule]"
                                value="{{ $tp['schedule'] ?? '' }}"
                                placeholder="Contoh: 12 - 20 Agustus 2026"
                                class="input-field"
                            >
                        </div>


                        <div class="sm:col-span-2">

                            <label class="lbl">
                                Risk
                            </label>

                            <textarea
                                name="test_plan[risk]"
                                rows="3"
                                placeholder="Potensi risiko dan mitigasi..."
                                class="input-field resize-none"
                            >{{ $tp['risk'] ?? '' }}</textarea>

                        </div>

                    </div>

                </div>


                <div class="flex items-center gap-3 pt-2">

                    <button
                        type="submit"
                        class="px-5 py-2.5 rounded-xl text-white text-xs font-bold transition"
                        style="background:#4f46e5;"
                    >
                        Simpan Perubahan
                    </button>


                    <button
                        type="button"
                        @click="showEditForm = false"
                        class="px-5 py-2.5 rounded-xl text-slate-300 text-xs font-semibold transition hover:text-white"
                        style="background:#1e293b;"
                    >
                        Batal
                    </button>

                </div>

            </form>

        </div>

    </main>

</div>


<x-profile-modal />


</body>

</html>
