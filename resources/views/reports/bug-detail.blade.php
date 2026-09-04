<!DOCTYPE html>

<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


<title>Bug Detail - QA Management</title>

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
        box-sizing: border-box;
    }

    body {
        background: #0c0f1a;
    }

    ::-webkit-scrollbar {
        width: 5px;
        height: 5px;
    }

    ::-webkit-scrollbar-track {
        background: #0c0f1a;
    }

    ::-webkit-scrollbar-thumb {
        background: rgba(99, 102, 241, 0.3);
        border-radius: 99px;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .fade-in {
        animation: fadeIn 0.4s ease both;
    }

    .timeline-dot::before {
        content: '';
        position: absolute;
        left: -1px;
        top: 0;
        bottom: -1rem;
        width: 1px;
        background: rgba(99, 102, 241, 0.15);
    }

    .timeline-dot:last-child::before {
        display: none;
    }
</style>


</head>

<body
    class="h-full text-slate-100 flex overflow-hidden"
    x-data="{ sidebarOpen: false }"
>


<x-sidebar />

<!-- MAIN CONTENT -->
<div class="flex-1 flex flex-col min-w-0 overflow-y-auto h-full">

    <!-- TOPBAR -->
    <header
        class="h-16 border-b border-white/[0.06] px-4 sm:px-8 flex items-center justify-between sticky top-0 z-30"
        style="background: rgba(12, 15, 26, 0.85); backdrop-filter: blur(12px);"
    >

        <div class="flex items-center gap-3">

            <button
                @click="$dispatch('toggle-sidebar')"
                class="md:hidden p-2 rounded-xl text-slate-400 hover:text-white border border-white/[0.06] cursor-pointer"
                style="background: #111827;"
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
                        d="M4 6h16M4 12h16M4 18h16"
                    />
                </svg>
            </button>

            <div class="flex items-center gap-2 text-xs text-slate-500 font-medium">

                <a
                    href="{{ route('report.bug-history') }}"
                    class="hover:text-indigo-400 transition"
                >
                    Riwayat Bug
                </a>

                <span class="text-slate-700">/</span>

                <span class="text-slate-300 truncate max-w-[200px]">
                    {{ $bug->title }}
                </span>

            </div>
        </div>

        <div class="flex items-center gap-3">

            <a
                href="{{ route('report.bug-history') }}"
                class="flex items-center gap-1.5 px-3 py-1.5 text-xs text-slate-400 hover:text-white border border-white/[0.06] rounded-xl transition"
                style="background: #111827;"
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
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"
                    />
                </svg>

                Kembali
            </a>

        </div>
    </header>


    <div class="p-6 lg:p-8 fade-in max-w-6xl mx-auto w-full space-y-6">

        <!-- PAGE HEADER -->

        @php
            $statusBadge = match ($bug->status) {
                'Open' => [
                    'class' => 'bg-red-400',
                    'style' => 'bg-red-500/[0.12] text-red-300 border-red-500/35',
                ],

                'In Progress' => [
                    'class' => 'bg-amber-400',
                    'style' => 'bg-amber-500/[0.12] text-amber-200 border-amber-500/35',
                ],

                'Done in Review' => [
                    'class' => 'bg-violet-400',
                    'style' => 'bg-violet-500/[0.12] text-violet-300 border-violet-500/35',
                ],

                'Closed' => [
                    'class' => 'bg-emerald-400',
                    'style' => 'bg-emerald-500/[0.12] text-emerald-300 border-emerald-500/35',
                ],

                'Reopened' => [
                    'class' => 'bg-pink-400',
                    'style' => 'bg-pink-500/[0.12] text-pink-300 border-pink-500/35',
                ],

                default => [
                    'class' => 'bg-indigo-400',
                    'style' => 'bg-indigo-500/[0.12] text-indigo-300 border-indigo-500/35',
                ],
            };
        @endphp


        <div class="bg-[#111827] border border-slate-800/80 rounded-2xl p-6">

            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">

                <div class="space-y-2 min-w-0">

                    <div class="flex items-center gap-2">

                        <span class="text-[10px] font-bold text-rose-400 uppercase tracking-widest">
                            Bug Report
                        </span>

                        <span class="text-slate-700">•</span>

                        <span class="text-[10px] font-mono text-slate-500">
                            #{{ $bug->id }}
                        </span>

                    </div>


                    <h1 class="text-2xl font-bold text-white tracking-tight leading-snug">
                        {{ $bug->title }}
                    </h1>


                    <div class="flex flex-wrap items-center gap-2 pt-1">

                        <!-- STATUS -->
                        <span
                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold border {{ $statusBadge['style'] }}"
                        >
                            <span
                                class="w-1.5 h-1.5 rounded-full {{ $statusBadge['class'] }}"
                            ></span>

                            {{ $bug->status }}
                        </span>


                        <!-- DUE DATE -->
                        @if ($bug->due_date)
                            <span
                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-medium text-slate-400 border border-white/[0.06] bg-[#0c0f1a]"
                            >
                                <svg
                                    class="w-3 h-3"
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

                                Due {{ $bug->due_date->format('d M Y') }}
                            </span>
                        @endif


                        <!-- FINISH DATE -->
                        @if ($bug->finish_date)
                            <span
                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-medium border"
                                style="background: rgba(16, 185, 129, 0.08); color: #6ee7b7; border-color: rgba(16, 185, 129, 0.25);"
                            >
                                <svg
                                    class="w-3 h-3"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>

                                Selesai {{ $bug->finish_date->format('d M Y') }}
                            </span>
                        @endif

                    </div>
                </div>


                <!-- REPORTER -->
                <div class="flex items-center gap-3 shrink-0">

                    @if ($bug->reporter)

                        <div class="text-right hidden sm:block">

                            <div class="text-[10px] text-slate-500 uppercase tracking-wider font-bold">
                                Reporter
                            </div>

                            <div class="text-xs text-slate-300 font-medium mt-0.5">
                                {{ $bug->reporter->name }}
                            </div>

                        </div>


                        <div
                            class="w-9 h-9 rounded-xl flex items-center justify-center text-xs font-bold text-white border border-white/[0.08]"
                            style="background: linear-gradient(135deg, #4f46e5, #7c3aed);"
                        >
                            {{ strtoupper(substr($bug->reporter->name, 0, 2)) }}
                        </div>

                    @endif

                </div>

            </div>
        </div>


        <!-- MAIN GRID -->

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- LEFT -->
            <div class="lg:col-span-2 space-y-6">

                <!-- INFORMASI BUG -->

                <div class="bg-[#111827] border border-slate-800/80 rounded-2xl overflow-hidden">

                    <div class="flex items-center gap-2 px-6 py-4 border-b border-slate-800/80">

                        <div
                            class="w-7 h-7 rounded-lg flex items-center justify-center"
                            style="background: rgba(99, 102, 241, 0.15);"
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
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                />
                            </svg>
                        </div>

                        <h2 class="text-sm font-bold text-white">
                            Informasi Bug
                        </h2>

                    </div>


                    <div class="p-6 space-y-5">

                        <!-- DESKRIPSI -->

                        <div>

                            <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">
                                Deskripsi
                            </div>

                            <div
                                class="text-sm text-slate-200 leading-relaxed p-4 rounded-xl"
                                style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.05);"
                            >
                                {{ $bug->description ?: '—' }}
                            </div>

                        </div>


                        <!-- EXPECTED RESULT -->

                        <div>

                            <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">
                                Expected Result
                            </div>

                            <div
                                class="text-sm text-slate-200 leading-relaxed p-4 rounded-xl"
                                style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.05);"
                            >
                                {{ $bug->expected_result ?: 'Tidak ada' }}
                            </div>

                        </div>


                        <!-- ATTACHMENT -->

                        @if ($bug->attachment)

                            <div>

                                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">
                                    Bukti Attachment
                                </div>

                                <a
                                    href="{{ $bug->attachment_url }}"
                                    target="_blank"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold text-indigo-400 hover:text-indigo-300 transition"
                                    style="background: rgba(99, 102, 241, 0.08); border: 1px solid rgba(99, 102, 241, 0.2);"
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
                                            d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"
                                        />
                                    </svg>

                                    Lihat Attachment

                                </a>

                            </div>

                        @endif


                        <!-- META GRID -->

                        <div class="grid grid-cols-2 gap-4 pt-5 border-t border-slate-800/80">

                            <!-- REPORTER -->

                            <div class="space-y-1">

                                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                                    Reporter
                                </div>

                                @if ($bug->reporter)

                                    <div class="flex items-center gap-2 mt-1.5">

                                        <div
                                            class="w-6 h-6 rounded-lg flex items-center justify-center text-[9px] font-bold text-white shrink-0"
                                            style="background: linear-gradient(135deg, #4f46e5, #7c3aed);"
                                        >
                                            {{ strtoupper(substr($bug->reporter->name, 0, 2)) }}
                                        </div>

                                        <span class="text-sm text-slate-200 font-medium">
                                            {{ $bug->reporter->name }}
                                        </span>

                                    </div>

                                @else

                                    <span class="text-sm text-slate-500">
                                        Unknown
                                    </span>

                                @endif

                            </div>


                            <!-- ASSIGNED TO -->

                            <div class="space-y-1">

                                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                                    Assigned To
                                </div>

                                @if ($bug->assignee)

                                    <div class="flex items-center gap-2 mt-1.5">

                                        <div
                                            class="w-6 h-6 rounded-lg flex items-center justify-center text-[9px] font-bold text-white shrink-0"
                                            style="background: linear-gradient(135deg, #0891b2, #0e7490);"
                                        >
                                            {{ strtoupper(substr($bug->assignee->name, 0, 2)) }}
                                        </div>

                                        <span class="text-sm text-slate-200 font-medium">
                                            {{ $bug->assignee->name }}
                                        </span>

                                    </div>

                                @else

                                    <span class="text-sm text-slate-500">
                                        Unassigned
                                    </span>

                                @endif

                            </div>


                            <!-- DUE DATE -->

                            <div class="space-y-1">

                                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                                    Due Date
                                </div>

                                <div
                                    class="text-sm font-medium mt-1 {{ $bug->due_date && $bug->due_date->isPast() && $bug->status !== 'Closed' ? 'text-rose-400' : 'text-slate-200' }}"
                                >
                                    {{ $bug->due_date ? $bug->due_date->format('d M Y') : '—' }}
                                </div>

                            </div>


                            <!-- FINISHED DATE -->

                            <div class="space-y-1">

                                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                                    Finished Date
                                </div>

                                <div class="text-sm font-medium text-slate-200 mt-1">

                                    @if ($bug->finish_date)

                                        <span class="text-emerald-400">
                                            {{ $bug->finish_date->format('d M Y') }}
                                        </span>

                                    @else

                                        <span class="text-slate-600">
                                            —
                                        </span>

                                    @endif

                                </div>

                            </div>

                        </div>

                    </div>
                </div>


                <!-- TEST CASE TERKAIT -->

                @if ($bug->testResult && $bug->testResult->testCase)

                    <div class="bg-[#111827] border border-slate-800/80 rounded-2xl overflow-hidden">

                        <div class="flex items-center gap-2 px-6 py-4 border-b border-slate-800/80">

                            <div
                                class="w-7 h-7 rounded-lg flex items-center justify-center"
                                style="background: rgba(16, 185, 129, 0.15);"
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
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                                    />
                                </svg>
                            </div>

                            <h2 class="text-sm font-bold text-white">
                                Test Case Terkait
                            </h2>

                        </div>


                        <div class="divide-y divide-slate-800/60">

                            @php

                                $tc = $bug->testResult->testCase;

                                $rows = [
                                    [
                                        'label' => 'Judul Test Case',
                                        'value' => $tc->title
                                    ],
                                    [
                                        'label' => 'Requirement',
                                        'value' => $tc->requirement?->description ?? '-'
                                    ],
                                    [
                                        'label' => 'Test Suite',
                                        'value' => $tc->testSuite?->name ?? '-'
                                    ],
                                    [
                                        'label' => 'Project',
                                        'value' => $tc->testSuite?->project?->name ?? '-'
                                    ],
                                ];

                            @endphp


                            @foreach ($rows as $row)

                                <div class="flex items-start justify-between gap-4 px-6 py-3.5">

                                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider shrink-0 pt-0.5">
                                        {{ $row['label'] }}
                                    </span>

                                    <span class="text-sm text-slate-200 text-right">
                                        {{ $row['value'] }}
                                    </span>

                                </div>

                            @endforeach

                        </div>

                    </div>

                @endif

            </div>


            <!-- RIGHT: ACTIVITY TIMELINE -->

            <div class="lg:col-span-1">

                <div class="bg-[#111827] border border-slate-800/80 rounded-2xl overflow-hidden sticky top-24">

                    <div class="flex items-center gap-2 px-5 py-4 border-b border-slate-800/80">

                        <div
                            class="w-7 h-7 rounded-lg flex items-center justify-center"
                            style="background: rgba(245, 158, 11, 0.15);"
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
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                        </div>

                        <h2 class="text-sm font-bold text-white">
                            Riwayat Aktivitas
                        </h2>


                        @if ($bug->histories->count() > 0)

                            <span
                                class="ml-auto px-2 py-0.5 rounded-full text-[9px] font-bold"
                                style="background: rgba(245, 158, 11, 0.15); color: #fde68a; border: 1px solid rgba(245, 158, 11, 0.25);"
                            >
                                {{ $bug->histories->count() }}
                            </span>

                        @endif

                    </div>


                    <div class="p-5">

                        @forelse ($bug->histories as $history)

                            @php

                                $hBadge = match ($history->field_name) {

                                    'status' => [
                                        'class' => 'bg-indigo-500/[0.12] text-indigo-300 border-indigo-500/25'
                                    ],

                                    'assigned_to' => [
                                        'class' => 'bg-emerald-500/[0.12] text-emerald-300 border-emerald-500/25'
                                    ],

                                    'description' => [
                                        'class' => 'bg-amber-500/[0.12] text-amber-200 border-amber-500/25'
                                    ],

                                    default => [
                                        'class' => 'bg-slate-500/[0.12] text-slate-400 border-slate-500/25'
                                    ],

                                };

                            @endphp


                            <div class="relative flex gap-3 pb-5 timeline-dot">

                                <!-- AVATAR -->

                                <div
                                    class="w-8 h-8 rounded-lg flex items-center justify-center text-[9px] font-bold text-white shrink-0 border border-white/[0.06]"
                                    style="background: linear-gradient(135deg, #4f46e5, #7c3aed);"
                                >
                                    {{ $history->changedBy ? strtoupper(substr($history->changedBy->name, 0, 2)) : 'SY' }}
                                </div>


                                <!-- CONTENT -->

                                <div class="flex-1 min-w-0">

                                    <div class="flex items-center justify-between gap-2 mb-1">

                                        <span class="text-xs font-semibold text-white truncate">
                                            {{ $history->changedBy?->name ?? 'System' }}
                                        </span>


                                        <span
                                            class="px-1.5 py-0.5 rounded-md text-[9px] font-bold shrink-0 border {{ $hBadge['class'] }}"
                                        >
                                            {{ ucfirst($history->field_name) }}
                                        </span>

                                    </div>


                                    <div class="text-[10px] text-slate-500 mb-2">
                                        {{ $history->created_at->format('d M Y, H:i') }}
                                    </div>


                                    @if ($history->description)

                                        <p class="text-xs text-slate-400 mb-2 leading-relaxed">
                                            {{ $history->description }}
                                        </p>

                                    @endif


                                    @if ($history->old_value && $history->new_value)

                                        <div class="flex items-center gap-1.5 text-[10px] flex-wrap">

                                            <span
                                                class="px-2 py-1 rounded-lg font-semibold"
                                                style="background: rgba(239, 68, 68, 0.1); color: #fca5a5; border: 1px solid rgba(239, 68, 68, 0.25);"
                                            >
                                                {{ $history->old_value }}
                                            </span>


                                            <svg
                                                class="w-3 h-3 text-slate-600 shrink-0"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M17 8l4 4m0 0l-4 4m4-4H3"
                                                />
                                            </svg>


                                            <span
                                                class="px-2 py-1 rounded-lg font-semibold"
                                                style="background: rgba(16, 185, 129, 0.1); color: #6ee7b7; border: 1px solid rgba(16, 185, 129, 0.25);"
                                            >
                                                {{ $history->new_value }}
                                            </span>

                                        </div>

                                    @endif

                                </div>

                            </div>

                        @empty

                            <div class="py-8 text-center">

                                <div
                                    class="w-10 h-10 rounded-2xl flex items-center justify-center mx-auto mb-3"
                                    style="background: rgba(99, 102, 241, 0.08); border: 1px solid rgba(99, 102, 241, 0.15);"
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
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                    </svg>
                                </div>

                                <p class="text-xs text-slate-500 font-medium">
                                    Belum ada riwayat aktivitas
                                </p>

                            </div>

                        @endforelse

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<x-profile-modal />


</body>

</html>
