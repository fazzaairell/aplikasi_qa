
<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Bug - TESTIFY</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('image/icon-aldo.png') }}">


    <script src="https://cdn.tailwindcss.com"></script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        * {
            font-family: 'Inter', sans-serif;
            box-sizing: border-box;
        }

        body {
            background: #080b14;
        }

        ::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }

        ::-webkit-scrollbar-track {
            background: #080b14;
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(99, 102, 241, 0.3);
            border-radius: 99px;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(16px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes pulseRing {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.4);
            }

            50% {
                box-shadow: 0 0 0 6px rgba(99, 102, 241, 0);
            }
        }

        .fade-up {
            animation: fadeInUp 0.4s ease both;
        }

        .slide-right {
            animation: slideInRight 0.45s ease both;
        }

        .avatar-ring {
            animation: pulseRing 2.5s ease-in-out infinite;
        }

        .card {
            background: #0e1220;
            border: 1px solid rgba(255, 255, 255, 0.055);
            border-radius: 16px;
            transition: border-color 0.2s;
        }

        .card:hover {
            border-color: rgba(255, 255, 255, 0.09);
        }

        .glass-nav {
            background: rgba(8, 11, 20, 0.88);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        .badge-open {
            background: rgba(239, 68, 68, 0.12);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.28);
        }

        .badge-progress {
            background: rgba(99, 102, 241, 0.12);
            color: #a5b4fc;
            border: 1px solid rgba(99, 102, 241, 0.28);
        }

        .badge-resolved {
            background: rgba(16, 185, 129, 0.12);
            color: #6ee7b7;
            border: 1px solid rgba(16, 185, 129, 0.28);
        }

        .badge-other {
            background: rgba(168, 85, 247, 0.12);
            color: #d8b4fe;
            border: 1px solid rgba(168, 85, 247, 0.28);
        }

        .pri-critical {
            background: rgba(239, 68, 68, 0.1);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.22);
        }

        .pri-high {
            background: rgba(249, 115, 22, 0.1);
            color: #fdba74;
            border: 1px solid rgba(249, 115, 22, 0.22);
        }

        .pri-medium {
            background: rgba(234, 179, 8, 0.1);
            color: #fde047;
            border: 1px solid rgba(234, 179, 8, 0.22);
        }

        .pri-low {
            background: rgba(100, 116, 139, 0.1);
            color: #94a3b8;
            border: 1px solid rgba(100, 116, 139, 0.22);
        }

        .nav-link {
            padding: 6px 14px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 500;
            color: #94a3b8;
            border: 1px solid transparent;
            transition: all 0.2s;
            text-decoration: none;
        }

        .nav-link:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.06);
            border-color: rgba(255, 255, 255, 0.08);
        }

        .nav-link.active {
            color: #fff;
            background: rgba(99, 102, 241, 0.15);
            border-color: rgba(99, 102, 241, 0.3);
        }

        .meta-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #4b5563;
            margin-bottom: 6px;
        }

        .meta-value {
            font-size: 14px;
            font-weight: 600;
            color: #e2e8f0;
        }

        .meta-muted {
            color: #94a3b8;
        }

        .section-icon {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .divider {
            border-color: rgba(255, 255, 255, 0.05);
            margin: 16px 0;
        }

        .attach-wrap {
            position: relative;
            display: block;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.07);
            transition: border-color 0.3s;
        }

        .attach-wrap:hover {
            border-color: rgba(99, 102, 241, 0.4);
        }

        .attach-wrap img {
            transition: transform 0.5s;
        }

        .attach-wrap:hover img {
            transform: scale(1.01);
        }

        .attach-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(
                to top,
                rgba(8, 11, 20, 0.75) 0%,
                transparent 55%
            );
            opacity: 0;
            transition: opacity 0.3s;
            border-radius: 12px;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            padding-bottom: 16px;
        }

        .attach-wrap:hover .attach-overlay {
            opacity: 1;
        }
    </style>
</head>

<body
    class="h-full text-slate-100 flex overflow-hidden"
    x-data="{ sidebarOpen: false }">


{{-- SIDEBAR --}}
<x-sidebar />


{{-- MAIN WRAPPER --}}
<div class="flex-1 flex flex-col min-w-0 overflow-y-auto h-full">


    @if(! auth()->user()->isDeveloper())

        {{-- TOPBAR: Non-Developer --}}
        <header
            class="h-16 px-4 sm:px-8 flex items-center justify-between sticky top-0 z-30 glass-nav">

            <div class="flex items-center gap-3">

                <button
                    @click="$dispatch('toggle-sidebar')"
                    class="md:hidden p-2 rounded-xl text-slate-400 hover:text-white border border-white/[0.06] cursor-pointer"
                    style="background:#111827;">

                    <svg
                        class="w-5 h-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />

                    </svg>

                </button>


                <a
                    href="{{ url()->previous() }}"
                    class="inline-flex items-center gap-2 text-sm font-semibold text-slate-400 hover:text-indigo-400 transition">

                    <svg
                        class="w-4 h-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15 19l-7-7 7-7" />

                    </svg>

                    Kembali

                </a>

            </div>


            <div class="text-xs text-slate-500 hidden sm:block">

                Bug Report /
                <span class="text-slate-300">
                    {{ Str::limit($bug->title, 30) }}
                </span>

            </div>

        </header>


    @endif


    {{-- MAIN --}}
    <main
        class="p-4 sm:p-6 lg:p-8 max-w-6xl mx-auto w-full space-y-6">


        {{-- HERO HEADER CARD --}}
        <div
            class="fade-up"
            style="animation-delay:0s;">

            <div class="card p-6 relative overflow-hidden">

                <div
                    class="absolute -top-12 -right-12 w-56 h-56 rounded-full opacity-[0.035]"
                    style="background:radial-gradient(circle,#6366f1,transparent 70%);">
                </div>


                <div
                    class="flex flex-col sm:flex-row sm:items-start justify-between gap-5 relative">

                    <div class="flex-1 min-w-0">

                        <div class="inline-flex items-center gap-1.5 mb-3">

                            <span
                                class="w-1.5 h-1.5 rounded-full bg-indigo-400 animate-pulse">
                            </span>

                            <span
                                class="text-[10px] font-bold tracking-[0.15em] uppercase text-indigo-400">

                                Detail Bug Report

                            </span>

                        </div>


                        <h1
                            class="text-2xl sm:text-3xl font-bold text-white tracking-tight leading-tight mb-4">

                            {{ $bug->title }}

                        </h1>


                        <div
                            class="flex flex-wrap items-center gap-4 text-xs text-slate-500">

                            <span class="flex items-center gap-1.5">

                                <svg
                                    class="w-3.5 h-3.5 text-slate-600"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />

                                </svg>

                                Dilaporkan
                                {{ $bug->created_at->format('d M Y, H:i') }}

                            </span>


                            <span class="flex items-center gap-1.5">

                                <svg
                                    class="w-3.5 h-3.5 text-slate-600"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />

                                </svg>

                                Oleh:

                                <span class="text-slate-300 font-medium">
                                    {{ $bug->reporter->name ?? 'Unknown' }}
                                </span>

                            </span>


                            @if($bug->due_date)

                                <span class="flex items-center gap-1.5">

                                    <svg
                                        class="w-3.5 h-3.5 text-slate-600"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24">

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />

                                    </svg>

                                    Due:

                                    <span class="text-slate-300 font-medium">
                                        {{ $bug->due_date->format('d M Y') }}
                                    </span>

                                </span>

                            @endif

                        </div>

                    </div>


                    {{-- STATUS + PRIORITY --}}
                    <div
                        class="flex flex-col items-end gap-3 shrink-0">

                        @php
                            $sc = match($bug->status) {
                                'Open' => 'badge-open',
                                'In Progress' => 'badge-progress',
                                'Resolved', 'Done in Review' => 'badge-resolved',
                                default => 'badge-other',
                            };

                            $sd = match($bug->status) {
                                'Open' => '#f87171',
                                'In Progress' => '#818cf8',
                                'Resolved', 'Done in Review' => '#34d399',
                                default => '#c084fc',
                            };
                        @endphp


                        <span
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold {{ $sc }}">

                            <span
                                class="w-2 h-2 rounded-full"
                                x-bind:style="'background: {{ $sd }}; box-shadow: 0 0 6px {{ $sd }};'">
                            </span>

                            {{ $bug->status }}

                        </span>


                        @php
                            $priority = $bug->testResult?->testCase?->priority ?? 'Low';

                            $pc = match($priority) {
                                'Critical' => 'pri-critical',
                                'High' => 'pri-high',
                                'Medium' => 'pri-medium',
                                default => 'pri-low',
                            };

                            $pe = match($priority) {
                                'Critical' => '🔴',
                                'High' => '🟠',
                                'Medium' => '🟡',
                                default => '⬜',
                            };
                        @endphp


                        <span
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold {{ $pc }}">

                            {{ $pe }} {{ $priority }}

                        </span>

                    </div>

                </div>

            </div>

        </div>


        {{-- BODY GRID --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">


            {{-- LEFT --}}
            <div class="lg:col-span-2 space-y-5">


                {{-- DESKRIPSI --}}
                <div
                    class="card p-6 fade-up"
                    style="animation-delay:0.05s;">

                    <div class="flex items-center gap-3 mb-5">

                        <div
                            class="section-icon"
                            style="background:rgba(99,102,241,0.12);">

                            <svg
                                class="w-4 h-4 text-indigo-400"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h7" />

                            </svg>

                        </div>


                        <h3 class="text-sm font-bold text-white">
                            Deskripsi Masalah
                        </h3>

                    </div>


                    <div
                        class="text-slate-300 text-sm leading-relaxed rounded-xl p-4"
                        style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.04);">

                        {!! nl2br(e($bug->description)) !!}

                    </div>

                </div>


                {{-- EXPECTED RESULT --}}
                <div
                    class="card p-6 fade-up"
                    style="animation-delay:0.10s;">

                    <div class="flex items-center gap-3 mb-5">

                        <div
                            class="section-icon"
                            style="background:rgba(16,185,129,0.12);">

                            <svg
                                class="w-4 h-4 text-emerald-400"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />

                            </svg>

                        </div>


                        <h3 class="text-sm font-bold text-white">
                            Expected Result
                        </h3>

                    </div>


                    <div
                        class="text-slate-300 text-sm leading-relaxed rounded-xl p-4"
                        style="background:rgba(16,185,129,0.04);border:1px solid rgba(16,185,129,0.08);">

                        {!! nl2br(e($bug->expected_result ?? 'Tidak ada data expected result.')) !!}

                    </div>

                </div>


                {{-- ATTACHMENT --}}
                @if($bug->attachment_url)

                    <div
                        class="card p-6 fade-up"
                        style="animation-delay:0.15s;">

                        <div class="flex items-center gap-3 mb-5">

                            <div
                                class="section-icon"
                                style="background:rgba(245,158,11,0.12);">

                                <svg
                                    class="w-4 h-4 text-amber-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />

                                </svg>

                            </div>


                            <h3 class="text-sm font-bold text-white">
                                Lampiran / Screenshot
                            </h3>

                        </div>


                        <a
                            href="{{ $bug->attachment_url }}"
                            target="_blank"
                            class="attach-wrap">

                            <img
                                src="{{ $bug->attachment_url }}"
                                alt="Bug Attachment"
                                class="w-full h-auto object-cover max-h-[420px]">


                            <div class="attach-overlay">

                                <span
                                    class="px-4 py-2 rounded-xl text-xs font-bold text-white flex items-center gap-2"
                                    style="background:rgba(99,102,241,0.8);backdrop-filter:blur(8px);">

                                    <svg
                                        class="w-4 h-4"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24">

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />

                                    </svg>

                                    Buka di Tab Baru

                                </span>

                            </div>

                        </a>

                    </div>

                @endif

                {{-- FIX ATTACHMENT (bukti perbaikan dari developer) --}}
                @if($bug->fix_attachment_url)

                    <div
                        class="card p-6 fade-up"
                        style="animation-delay:0.18s;">

                        <div class="flex items-center gap-3 mb-5">

                            <div
                                class="section-icon"
                                style="background:rgba(52,211,153,0.12);">

                                <svg
                                    class="w-4 h-4 text-emerald-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 12.75l2.25 2.25 7.5-7.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />

                                </svg>

                            </div>


                            <h3 class="text-sm font-bold text-white">
                                Bukti Perbaikan (Developer)
                            </h3>

                        </div>


                        <a
                            href="{{ $bug->fix_attachment_url }}"
                            target="_blank"
                            class="attach-wrap">

                            <img
                                src="{{ $bug->fix_attachment_url }}"
                                alt="Bug Fix Attachment"
                                class="w-full h-auto object-cover max-h-[420px]">

                            <div class="attach-overlay">

                                <span
                                    class="px-4 py-2 rounded-xl text-xs font-bold text-white flex items-center gap-2"
                                    style="background:rgba(16,185,129,0.8);backdrop-filter:blur(8px);">

                                    Buka di Tab Baru

                                </span>

                            </div>

                        </a>

                    </div>

                @endif

            </div>


            {{-- RIGHT --}}
            <div
                class="space-y-5 slide-right"
                style="animation-delay:0.08s;">


                {{-- INFO CARD --}}
                <div class="card p-5">

                    <div
                        class="meta-label"
                        style="margin-bottom:16px;">

                        Informasi Bug

                    </div>


                    {{-- PROJECT --}}
                    <div class="flex items-start gap-3 mb-4">

                        <div
                            class="w-8 h-8 rounded-lg shrink-0 flex items-center justify-center"
                            style="background:rgba(99,102,241,0.1);">

                            <svg
                                class="w-4 h-4 text-indigo-400"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />

                            </svg>

                        </div>


                        <div class="min-w-0">

                            <div class="meta-label">
                                Project
                            </div>

                            <div class="meta-value truncate">
                                {{ $bug->testResult?->testCase?->testSuite?->project?->name ?? '-' }}
                            </div>

                        </div>

                    </div>


                    <hr class="divider">


                    {{-- TEST SUITE --}}
                    <div class="flex items-start gap-3 mb-4">

                        <div
                            class="w-8 h-8 rounded-lg shrink-0 flex items-center justify-center"
                            style="background:rgba(16,185,129,0.1);">

                            <svg
                                class="w-4 h-4 text-emerald-400"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />

                            </svg>

                        </div>


                        <div class="min-w-0">

                            <div class="meta-label">
                                Test Suite
                            </div>

                            <div class="meta-value meta-muted truncate">
                                {{ $bug->testResult?->testCase?->testSuite?->name ?? '-' }}
                            </div>

                        </div>

                    </div>


                    {{-- TEST CASE --}}
                    <div class="flex items-start gap-3 mb-4">

                        <div
                            class="w-8 h-8 rounded-lg shrink-0 flex items-center justify-center"
                            style="background:rgba(245,158,11,0.1);">

                            <svg
                                class="w-4 h-4 text-amber-400"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2M9 9h6M9 13h6" />

                            </svg>

                        </div>


                        <div class="min-w-0">

                            <div class="meta-label">
                                Test Case
                            </div>

                            <div class="meta-value meta-muted truncate">
                                {{ $bug->testResult?->testCase?->title ?? '-' }}
                            </div>

                        </div>

                    </div>


                    {{-- REQUIREMENT --}}
                    <div class="flex items-start gap-3">

                        <div
                            class="w-8 h-8 rounded-lg shrink-0 flex items-center justify-center"
                            style="background:rgba(168,85,247,0.1);">

                            <svg
                                class="w-4 h-4 text-purple-400"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />

                            </svg>

                        </div>


                        <div class="min-w-0">

                            <div class="meta-label">
                                Requirement
                            </div>

                            <div class="meta-value meta-muted truncate">
                                {{ $bug->testResult?->testCase?->requirement?->title ?? '-' }}
                            </div>

                        </div>

                    </div>

                </div>


                {{-- ASSIGNMENT CARD --}}
                <div class="card p-5">

                    <div
                        class="meta-label"
                        style="margin-bottom:16px;">

                        Assignment

                    </div>


                    {{-- ASSIGNED TO --}}
                    <div class="mb-4">

                        <div class="meta-label">
                            Assigned To
                        </div>


                        @if($bug->assignee)

                            <div class="flex items-center gap-3 mt-2">

                                <div
                                    class="w-9 h-9 rounded-xl flex items-center justify-center text-white text-xs font-bold shrink-0 avatar-ring"
                                    style="background:linear-gradient(135deg,#4f46e5,#7c3aed);">

                                    {{ strtoupper(substr($bug->assignee->name, 0, 2)) }}

                                </div>


                                <div>

                                    <div class="text-sm font-semibold text-white">
                                        {{ $bug->assignee->name }}
                                    </div>

                                    <div class="text-[11px] text-slate-500">
                                        {{ $bug->assignee->role ?? 'Developer' }}
                                    </div>

                                </div>

                            </div>

                        @else

                            <div class="flex items-center gap-2 mt-2">

                                <div
                                    class="w-9 h-9 rounded-xl border border-dashed border-white/20 flex items-center justify-center text-slate-600">

                                    <svg
                                        class="w-4 h-4"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24">

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />

                                    </svg>

                                </div>


                                <span class="text-sm text-slate-600 italic">
                                    Belum di-assign
                                </span>

                            </div>

                        @endif

                    </div>


                    <hr class="divider">


                    {{-- DUE DATE --}}
                    <div class="mb-4">

                        <div class="meta-label">
                            Due Date
                        </div>


                        @if($bug->due_date)

                            @php
                                $isOverdue =
                                    $bug->due_date->isPast() &&
                                    !in_array(
                                        $bug->status,
                                        ['Resolved', 'Done in Review']
                                    );
                            @endphp


                            <div class="flex items-center gap-2 mt-1.5">

                                <svg
                                    class="w-4 h-4 {{ $isOverdue ? 'text-red-400' : 'text-slate-500' }}"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />

                                </svg>


                                <span
                                    class="text-sm font-semibold {{ $isOverdue ? 'text-red-400' : 'text-slate-200' }}">

                                    {{ $bug->due_date->format('d M Y') }}

                                </span>


                                @if($isOverdue)

                                    <span
                                        class="text-[10px] font-bold text-red-400 px-1.5 py-0.5 rounded"
                                        style="background:rgba(239,68,68,0.1);">

                                        Overdue

                                    </span>

                                @endif

                            </div>

                        @else

                            <span class="text-sm text-slate-600 mt-1 block">
                                –
                            </span>

                        @endif

                    </div>


                    <hr class="divider">


                    {{-- START DATE & FINISH DATE (diisi Developer) --}}
                    <div class="grid grid-cols-2 gap-4 mb-4">

                        <div>
                            <div class="meta-label">
                                Start Date
                            </div>

                            @if($bug->start_date)
                                <span class="text-sm font-semibold text-slate-200 mt-1.5 block">
                                    {{ $bug->start_date->format('d M Y') }}
                                </span>
                            @else
                                <span class="text-sm text-slate-600 mt-1.5 block">
                                    –
                                </span>
                            @endif
                        </div>

                        <div>
                            <div class="meta-label">
                                Finish Date
                            </div>

                            @if($bug->finish_date)
                                <span class="text-sm font-semibold text-slate-200 mt-1.5 block">
                                    {{ $bug->finish_date->format('d M Y') }}
                                </span>
                            @else
                                <span class="text-sm text-slate-600 mt-1.5 block">
                                    –
                                </span>
                            @endif
                        </div>

                    </div>


                    <hr class="divider">


                    {{-- PRIORITY --}}
                    <div>

                        <div class="meta-label">
                            Priority
                        </div>


                        <div class="mt-1.5">

                            @if($priority === 'Critical')

                                <span
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold pri-critical">

                                    🔴 Critical

                                </span>

                            @elseif($priority === 'High')

                                <span
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold pri-high">

                                    🟠 High

                                </span>

                            @elseif($priority === 'Medium')

                                <span
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold pri-medium">

                                    🟡 Medium

                                </span>

                            @else

                                <span
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold pri-low">

                                    ⬜ Low

                                </span>

                            @endif

                        </div>

                    </div>

                </div>


                {{-- QUICK ACTIONS --}}
                @if(auth()->user()->isDeveloper())

                    <div class="card p-5">

                        <div
                            class="meta-label"
                            style="margin-bottom:16px;">

                            Quick Actions

                        </div>


                        <div class="space-y-2">

                            <a
                                href="{{ route('bugs.index') }}"
                                class="flex items-center gap-3 w-full px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-300 hover:text-white border border-white/[0.06] hover:border-white/10 hover:bg-white/[0.04] transition">

                                <svg
                                    class="w-4 h-4 text-slate-500"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 10h16M4 14h16M4 18h16" />

                                </svg>

                                Semua Bug Report

                            </a>


                            <a
                                href="{{ route('dashboard.developer') }}"
                                class="flex items-center gap-3 w-full px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-300 hover:text-white border border-white/[0.06] hover:border-indigo-500/30 hover:bg-indigo-500/[0.06] transition">

                                <svg
                                    class="w-4 h-4 text-indigo-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zM14 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z" />

                                </svg>

                                Dashboard

                            </a>

                        </div>

                    </div>

                @endif

            </div>

        </div>

    </main>

</div>


<x-profile-modal />

</body>
</html>

