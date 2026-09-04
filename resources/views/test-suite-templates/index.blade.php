```blade
<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Template Test Suite - QA Management</title>

    <meta name="description"
        content="Kelola template test suite untuk digunakan kembali di berbagai proyek.">

    <script src="https://cdn.tailwindcss.com"></script>

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <script defer
        src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        * {
            font-family: 'Inter', sans-serif;
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
            background: rgba(139, 92, 246, .3);
            border-radius: 99px;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(12px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-in {
            animation: fadeInUp .35s ease both;
        }

        .card-hover {
            transition: border-color .2s, box-shadow .2s, transform .2s;
        }

        .card-hover:hover {
            border-color: rgba(139, 92, 246, .4);
            box-shadow:
                0 0 0 1px rgba(139, 92, 246, .15),
                0 8px 32px rgba(0, 0, 0, .4);
            transform: translateY(-2px);
        }
    </style>
</head>

<body
    class="h-full font-sans text-slate-100 flex overflow-hidden"
    x-data="{
        sidebarOpen: false,
        collapsed: false,
        previewTemplate: null,
        renameModal: {
            show: false,
            id: null,
            name: ''
        },
        useModal: {
            show: false,
            id: null,
            name: '',
            count: 0
        },
        deleteModal: {
            show: false,
            id: null,
            name: ''
        }
    }"
>

    <x-sidebar />

    <!-- MAIN CONTENT -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto h-full">

        <!-- TOPBAR -->
        <header
            class="h-16 border-b px-4 sm:px-8 flex items-center justify-between sticky top-0 z-30"
            style="background: rgba(12,15,26,.9); backdrop-filter: blur(12px); border-color: rgba(255,255,255,.06);"
        >
            <div class="flex items-center space-x-4">

                <button
                    @click="$dispatch('toggle-sidebar')"
                    class="md:hidden text-slate-400 hover:text-white p-2 rounded-lg bg-[#131b2e] border border-slate-800 cursor-pointer"
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
                        ></path>
                    </svg>
                </button>

                <div>
                    <div class="text-xs text-violet-400 font-semibold tracking-wider">
                        PENGUJIAN
                    </div>

                    <div class="text-sm font-bold text-white">
                        Template Test Suite
                    </div>
                </div>
            </div>

            <a
                href="{{ route('test-suites.index') }}"
                class="flex items-center gap-2 px-4 py-2 rounded-xl bg-[#131b2e] border border-slate-800 hover:border-slate-600 text-slate-400 hover:text-white text-xs font-semibold transition"
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
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"
                    />
                </svg>

                Kembali ke Test Suites
            </a>
        </header>

        <main class="p-8 space-y-8">

            <!-- PAGE HEADER -->
            <div class="flex items-start justify-between">

                <div>
                    <div class="text-xs text-violet-400 font-semibold tracking-wider mb-1">
                        TEMPLATE LIBRARY
                    </div>

                    <h1 class="text-3xl font-bold text-white tracking-tight">
                        Template Test Suite
                    </h1>

                    <p class="text-sm text-slate-400 mt-2">
                        Simpan struktur test suite sebagai template dan gunakan kembali di project manapun.
                    </p>
                </div>

                <div class="flex items-center gap-2 mt-1">
                    <div
                        class="px-4 py-2 rounded-xl bg-violet-500/10 border border-violet-500/20 text-violet-300 text-xs font-semibold"
                    >
                        {{ $templates->count() }} Template Tersimpan
                    </div>
                </div>

            </div>

            @if(session('success'))

                <div
                    class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-semibold flex items-center gap-2 animate-in"
                >
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
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>

                    {{ session('success') }}
                </div>

            @endif

            @if($templates->isEmpty())

                <!-- EMPTY STATE -->
                <div
                    class="flex flex-col items-center justify-center py-24 bg-[#131b2e] border border-slate-800/60 rounded-2xl text-center animate-in"
                >

                    <div
                        class="w-20 h-20 rounded-2xl flex items-center justify-center mb-6"
                        style="background: rgba(139,92,246,0.1); border: 1px solid rgba(139,92,246,0.2);"
                    >
                        <svg
                            class="w-10 h-10 text-violet-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.5"
                                d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"
                            />
                        </svg>
                    </div>

                    <h2 class="text-lg font-bold text-white mb-2">
                        Belum Ada Template
                    </h2>

                    <p class="text-slate-400 text-sm max-w-sm mb-6">
                        Buka halaman
                        <strong class="text-slate-300">Test Suites</strong>,
                        pilih sebuah test suite, lalu klik tombol
                        <span class="px-1.5 py-0.5 rounded bg-violet-500/20 text-violet-300 font-semibold text-xs">
                            Template
                        </span>
                        untuk menyimpannya sebagai template.
                    </p>

                    <a
                        href="{{ route('test-suites.index') }}"
                        class="px-5 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-500 text-white text-xs font-semibold transition shadow-lg shadow-violet-600/20"
                    >
                        Buka Test Suites
                    </a>

                </div>

            @else

                <!-- TEMPLATE GRID -->
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

                    @foreach($templates as $i => $template)

                        <div
                            class="animate-in card-hover bg-[#131b2e] border border-slate-800/60 rounded-2xl overflow-hidden shadow-xl flex flex-col"
                            x-bind:style="'animation-delay: ' + ({{ $i }} * 0.05) + 's'"
                            x-data="{ expanded: false }"
                        >

                            <!-- CARD HEADER -->
                            <div class="p-5 flex-1">

                                <div class="flex items-start justify-between gap-3 mb-3">

                                    <div class="flex items-center gap-3 min-w-0">

                                        <div
                                            class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-sm shrink-0"
                                            style="background: rgba(139,92,246,0.15); color: #c4b5fd; border: 1px solid rgba(139,92,246,0.25);"
                                        >
                                            {{ strtoupper(substr($template->name, 0, 2)) }}
                                        </div>

                                        <div class="min-w-0">

                                            <h3 class="text-sm font-bold text-white leading-snug truncate">
                                                {{ $template->name }}
                                            </h3>

                                            <div class="text-[10px] text-slate-500 mt-0.5">
                                                Dibuat oleh
                                                <span class="text-slate-400">
                                                    {{ optional($template->creator)->name ?? 'Unknown' }}
                                                </span>

                                                ·

                                                {{ $template->created_at->diffForHumans() }}
                                            </div>

                                        </div>
                                    </div>

                                    <span
                                        class="px-2.5 py-1 rounded-full bg-violet-500/10 border border-violet-500/20 text-violet-300 text-[10px] font-bold whitespace-nowrap shrink-0"
                                    >
                                        {{ $template->testCaseTemplates->count() }} TC
                                    </span>

                                </div>

                                @if($template->description)

                                    <p class="text-xs text-slate-400 mb-3 leading-relaxed">
                                        {{ Str::limit($template->description, 100) }}
                                    </p>

                                @endif

                                <!-- PREVIEW TEST CASES -->
                                <div
                                    class="rounded-xl overflow-hidden border border-slate-800/60 bg-[#0b0f19]/50"
                                >

                                    <button
                                        type="button"
                                        @click="expanded = !expanded"
                                        class="w-full px-4 py-2.5 flex items-center justify-between text-left text-[11px] font-semibold text-slate-400 hover:text-slate-200 transition cursor-pointer"
                                    >
                                        <span>
                                            Preview Test Cases
                                        </span>

                                        <svg
                                            class="w-3.5 h-3.5 transition-transform"
                                            :class="expanded ? 'rotate-180' : ''"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M19 9l-7 7-7-7"
                                            />
                                        </svg>
                                    </button>

                                    <div
                                        x-show="expanded"
                                        x-collapse
                                        class="border-t border-slate-800/60"
                                    >

                                        @forelse($template->testCaseTemplates as $j => $tct)

                                            <div
                                                class="px-4 py-2.5 flex items-start gap-3 {{ $j < $template->testCaseTemplates->count() - 1 ? 'border-b border-slate-800/40' : '' }}"
                                            >

                                                <span
                                                    class="mt-0.5 w-4 h-4 rounded shrink-0 flex items-center justify-center text-[9px] font-bold"
                                                    style="background: rgba(139,92,246,0.15); color: #a78bfa;"
                                                >
                                                    {{ $j + 1 }}
                                                </span>

                                                <div class="min-w-0 flex-1">

                                                    <div
                                                        class="text-[11px] font-semibold text-slate-200 truncate"
                                                    >
                                                        {{ $tct->title }}
                                                    </div>

                                                    @php
                                                        $pColor = match($tct->priority) {
                                                            'Critical' => 'text-rose-400',
                                                            'High' => 'text-amber-400',
                                                            'Medium' => 'text-indigo-400',
                                                            default => 'text-slate-500'
                                                        };
                                                    @endphp

                                                    <span
                                                        class="text-[10px] font-semibold {{ $pColor }}"
                                                    >
                                                        ↑ {{ $tct->priority }}
                                                    </span>

                                                </div>

                                            </div>

                                        @empty

                                            <div class="px-4 py-3 text-xs text-slate-500">
                                                Tidak ada test case dalam template ini.
                                            </div>

                                        @endforelse

                                    </div>
                                </div>

                            </div>

                            <!-- CARD ACTIONS -->
                            <div
                                class="px-5 py-3 border-t border-slate-800/60 bg-[#0b0f19]/30 flex items-center justify-between gap-2"
                            >

                                <!-- Gunakan Template -->
                                <button
                                    type="button"
                                    @click="useModal = {
                                        show: true,
                                        id: {{ $template->id }},
                                        name: '{{ addslashes($template->name) }}',
                                        count: {{ $template->testCaseTemplates->count() }}
                                    }"
                                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-violet-600 hover:bg-violet-500 text-white text-[11px] font-semibold transition cursor-pointer shadow-sm shadow-violet-600/30"
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
                                            d="M12 4v16m8-8H4"
                                        />
                                    </svg>

                                    Gunakan
                                </button>

                                <div class="flex items-center gap-2">

                                    <!-- Rename -->
                                    <button
                                        type="button"
                                        @click="renameModal = {
                                            show: true,
                                            id: {{ $template->id }},
                                            name: '{{ addslashes($template->name) }}'
                                        }"
                                        title="Rename template"
                                        class="p-1.5 rounded-lg text-slate-500 hover:text-indigo-400 hover:bg-indigo-500/10 border border-transparent hover:border-indigo-500/20 transition cursor-pointer"
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
                                    </button>

                                    <!-- Hapus -->
                                    <button
                                        type="button"
                                        @click="deleteModal = {
                                            show: true,
                                            id: {{ $template->id }},
                                            name: '{{ addslashes($template->name) }}'
                                        }"
                                        title="Hapus template"
                                        class="p-1.5 rounded-lg text-slate-600 hover:text-red-400 hover:bg-red-500/10 border border-transparent hover:border-red-500/20 transition cursor-pointer"
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
                                    </button>

                                </div>
                            </div>

                        </div>

                    @endforeach

                </div>

            @endif

        </main>
    </div>


    <!-- =============================================
         MODAL: GUNAKAN TEMPLATE
         ============================================= -->

    <div
        x-show="useModal.show"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4"
        style="display: none;"
    >

        <div
            @click.away="useModal.show = false"
            class="w-full max-w-md p-6 rounded-2xl bg-[#131b2e] border border-slate-800 shadow-2xl space-y-4"
        >

            <div class="flex items-center justify-between border-b border-slate-800 pb-3">

                <div>

                    <h3 class="text-sm font-bold text-white">
                        Gunakan Template
                    </h3>

                    <p
                        class="text-[10px] text-slate-400 mt-0.5"
                        x-text="'&quot;' + useModal.name + '&quot; · ' + useModal.count + ' test cases'"
                    ></p>

                </div>

                <button
                    @click="useModal.show = false"
                    class="text-slate-400 hover:text-white text-xl font-bold cursor-pointer leading-none"
                >
                    &times;
                </button>

            </div>

            <form
                action="{{ route('test-suite-templates.use') }}"
                method="POST"
                class="space-y-4"
            >

                @csrf

                <input
                    type="hidden"
                    name="template_id"
                    x-bind:value="useModal.id"
                >

                <div>

                    <label class="block text-[11px] font-bold text-slate-400 mb-1">
                        Project Tujuan
                    </label>

                    <select
                        name="project_id"
                        required
                        class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-violet-500 text-xs"
                    >
                        <option value="">
                            -- Pilih Project --
                        </option>

                        @foreach(\App\Models\Project::orderBy('name')->get() as $proj)

                            <option value="{{ $proj->id }}">
                                {{ $proj->name }}
                            </option>

                        @endforeach

                    </select>

                </div>

                <div>

                    <label class="block text-[11px] font-bold text-slate-400 mb-1">
                        Nama Suite Baru
                        <span class="text-slate-600 font-normal">
                            (opsional — default: nama template)
                        </span>
                    </label>

                    <input
                        type="text"
                        name="suite_name"
                        :placeholder="useModal.name"
                        class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-violet-500 text-xs placeholder-slate-600"
                    >

                </div>

                <div
                    class="p-3 rounded-xl bg-violet-500/5 border border-violet-500/20 text-[10px] text-violet-300 flex items-start gap-2"
                >

                    <svg
                        class="w-3.5 h-3.5 shrink-0 mt-0.5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>

                    <span>
                        Test case akan di-<strong>copy</strong> secara independen.
                        Mengedit suite baru tidak akan mempengaruhi template asli.
                    </span>

                </div>

                <div class="flex items-center justify-end gap-3 pt-1">

                    <button
                        type="button"
                        @click="useModal.show = false"
                        class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition cursor-pointer"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="px-5 py-2 rounded-xl bg-violet-600 hover:bg-violet-500 text-white text-xs font-bold transition cursor-pointer shadow-lg shadow-violet-600/20"
                    >
                        Generate Test Suite
                    </button>

                </div>

            </form>

        </div>
    </div>


    <!-- =============================================
         MODAL: RENAME TEMPLATE
         ============================================= -->

    <div
        x-show="renameModal.show"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4"
        style="display: none;"
    >

        <div
            @click.away="renameModal.show = false"
            class="w-full max-w-sm p-6 rounded-2xl bg-[#131b2e] border border-slate-800 shadow-2xl space-y-4"
        >

            <div class="flex items-center justify-between border-b border-slate-800 pb-3">

                <h3 class="text-sm font-bold text-white">
                    Rename Template
                </h3>

                <button
                    @click="renameModal.show = false"
                    class="text-slate-400 hover:text-white text-xl font-bold cursor-pointer leading-none"
                >
                    &times;
                </button>

            </div>

            <form
                x-bind:action="`/test-suite-templates/${renameModal.id}/rename`"
                method="POST"
                class="space-y-4"
            >

                @csrf
                @method('PATCH')

                <div>

                    <label class="block text-[11px] font-bold text-slate-400 mb-1">
                        Nama Baru
                    </label>

                    <input
                        type="text"
                        name="name"
                        x-model="renameModal.name"
                        required
                        class="w-full px-4 py-2.5 bg-[#0b0f19] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-indigo-500 text-xs"
                    >

                </div>

                <div class="flex items-center justify-end gap-3">

                    <button
                        type="button"
                        @click="renameModal.show = false"
                        class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition cursor-pointer"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold transition cursor-pointer"
                    >
                        Simpan
                    </button>

                </div>

            </form>

        </div>
    </div>


    <!-- =============================================
         MODAL: HAPUS TEMPLATE
         ============================================= -->

    <div
        x-show="deleteModal.show"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4"
        style="display: none;"
    >

        <div
            @click.away="deleteModal.show = false"
            class="w-full max-w-sm p-6 rounded-2xl bg-[#131b2e] border border-red-500/20 shadow-2xl space-y-4"
        >

            <div class="flex items-start gap-4">

                <div
                    class="w-10 h-10 rounded-xl bg-red-500/10 border border-red-500/20 flex items-center justify-center shrink-0"
                >
                    <svg
                        class="w-5 h-5 text-red-400"
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

                <div>

                    <h3 class="text-sm font-bold text-white">
                        Hapus Template?
                    </h3>

                    <p class="text-xs text-slate-400 mt-1">
                        Template
                        "<span
                            class="text-slate-200"
                            x-text="deleteModal.name"
                        ></span>"
                        beserta semua test case template di dalamnya akan dihapus permanen.
                    </p>

                    <p class="text-xs text-slate-500 mt-1">
                        Test Suite yang sudah di-generate dari template ini
                        <strong class="text-slate-400">
                            tidak akan terpengaruh
                        </strong>.
                    </p>

                </div>

            </div>

            <form
                x-bind:action="`/test-suite-templates/${deleteModal.id}`"
                method="POST"
                class="flex items-center justify-end gap-3 pt-1"
            >

                @csrf
                @method('DELETE')

                <button
                    type="button"
                    @click="deleteModal.show = false"
                    class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition cursor-pointer"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-500 text-white text-xs font-semibold transition cursor-pointer"
                >
                    Hapus Permanen
                </button>

            </form>

        </div>
    </div>


    <x-profile-modal />

</body>

</html>
```
