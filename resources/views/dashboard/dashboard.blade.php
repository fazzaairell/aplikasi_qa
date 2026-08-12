<!DOCTYPE html>
<html lang="id" class="h-full bg-[#0b0f19]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - QA Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-full font-sans text-slate-100 flex overflow-hidden" x-data="{ sidebarOpen: false }">

<x-sidebar />

    <!-- MAIN CONTENT -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto bg-[#0b0f19] h-full">
        
        <!-- TOPBAR -->
        <header class="h-20 border-b border-slate-800/80 px-8 flex items-center justify-between sticky top-0 bg-[#0b0f19]/80 backdrop-blur-md z-30">
            <div class="flex items-center space-x-4">
                <button @click="sidebarOpen = !sidebarOpen" class="md:hidden text-slate-400 hover:text-white p-2 rounded-lg bg-[#131b2e] border border-slate-800 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
            </div>
            <div class="flex items-center space-x-4">
            </div>
        </header>

        <!-- DASHBOARD BODY CONTENT -->
        <main class="p-8 space-y-6">
            <div>
                <div class="text-[11px] text-indigo-400 font-bold tracking-widest uppercase mb-1">OVERVIEW</div>
                <h1 class="text-2xl font-bold text-white tracking-tight">Dashboard</h1>
            </div>

            <!-- KARTU STATISTIK ATAS (4 CARD) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Card 1: Total Proyek -->
                <div class="bg-[#131b2e] border border-slate-800/80 rounded-2xl p-5 relative overflow-hidden shadow-lg hover:border-indigo-500/50
          hover:bg-[#17213a]
          hover:scale-105
          transition duration-300 cursor-pointer
          rounded-3xl"">
                    <div class="absolute top-4 right-4 w-10 h-10 rounded-xl bg-indigo-500/20 border border-indigo-500/30 flex items-center justify-center text-indigo-400 ">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                    </div>
                    <div class="text-3xl font-extrabold text-white mb-1">{{ $totalProjects }}</div>
                    <div class="text-xs font-bold text-slate-200">Total Proyek</div>
                    <div class="text-[10px] text-slate-400 mt-0.5">aktif</div>
                </div>
  

                <!-- Card 2: Pass Rate -->
                <div class="bg-[#131b2e] border border-slate-800/80 rounded-2xl p-5 relative overflow-hidden shadow-lg hover:border-indigo-500/50
          hover:bg-[#17213a]
          hover:scale-105
          transition duration-300 cursor-pointer
          rounded-3xl">
                    <div class="absolute top-4 right-4 w-10 h-10 rounded-xl bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center text-emerald-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <div class="text-3xl font-extrabold text-white mb-1">{{ $passRate }}%</div>
                    <div class="text-xs font-bold text-slate-200">Pass Rate</div>
                    <div class="text-[10px] text-slate-400 mt-0.5">2 dari 5 passed</div>
                </div>

                <!-- Card 3: Bug Aktif -->
                <div class="bg-[#131b2e] border border-slate-800/80 rounded-2xl p-5 relative overflow-hidden shadow-lg hover:border-indigo-500/50
          hover:bg-[#17213a]
          hover:scale-105
          transition duration-300 cursor-pointer
          rounded-3xl"">
                    <div class="absolute top-4 right-4 w-10 h-10 rounded-xl bg-amber-500/20 border border-amber-500/30 flex items-center justify-center text-amber-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <div class="text-3xl font-extrabold text-white mb-1">3</div>
                    <div class="text-xs font-bold text-slate-200">Bug Aktif</div>
                    <div class="text-[10px] text-slate-400 mt-0.5">open/in progress</div>
                </div>

                <!-- Card 4: Blocked -->
                <div class="bg-[#131b2e] border border-slate-800/80 rounded-2xl p-5 relative overflow-hidden shadow-lg hover:border-indigo-500/50
          hover:bg-[#17213a]
          hover:scale-105
          transition duration-300 cursor-pointer
          rounded-3xl">
                    <div class="absolute top-4 right-4 w-10 h-10 rounded-xl bg-rose-500/20 border border-rose-500/30 flex items-center justify-center text-rose-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                    </div>
                    <div class="text-3xl font-extrabold text-white mb-1">1</div>
                    <div class="text-xs font-bold text-slate-200">Blocked</div>
                    <div class="text-[10px] text-slate-400 mt-0.5">test tertunda</div>
                </div>
            </div>

            <!-- BAGIAN TENGAH (2 KOLOM: TEST RUN AKTIF & BUG TERBARU) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- KIRI: TEST RUN AKTIF (2 Kolom span) -->
                <div class="lg:col-span-2 bg-[#131b2e] border border-slate-800/80 rounded-2xl p-6 shadow-xl space-y-5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-bold text-white tracking-wide">Test Run Aktif</h3>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 flex items-center space-x-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 animate-pulse"></span>
                            <span>Live</span>
                        </span>
                    </div>

                    <div class="space-y-4">
                        <!-- Test Run Item 1 -->
                        <div class="bg-[#0b0f19] border border-slate-800/80 rounded-xl p-4 space-y-3">
                            <div class="flex items-start justify-between">
                                <div>
                                    <div class="text-sm font-bold text-white">Sprint 3 — Regression Test</div>
                                    <div class="text-[11px] text-slate-400">E-Commerce Platform</div>
                                </div>
                                <span class="w-2 h-2 rounded-full bg-emerald-500 mt-1"></span>
                            </div>

                            <!-- Statistik Status Kecil -->
                            <div class="flex flex-wrap gap-2 text-[11px] font-bold">
                                <span class="text-emerald-400">22 Passed</span>
                                <span class="text-slate-600">•</span>
                                <span class="text-rose-400">4 Failed</span>
                                <span class="text-slate-600">•</span>
                                <span class="text-amber-400">2 Blocked</span>
                                <span class="text-slate-600">•</span>
                                <span class="text-slate-400">8 Untested</span>
                            </div>

                            <!-- Progress Bar Multiwarna -->
                            <div class="w-full h-2 bg-slate-800 rounded-full overflow-hidden flex">
                                <div class="bg-emerald-500 h-full" style="width: 60%"></div>
                                <div class="bg-rose-500 h-full" style="width: 15%"></div>
                                <div class="bg-amber-500 h-full" style="width: 5%"></div>
                            </div>

                            <div class="text-[10px] text-slate-400 flex justify-between">
                                <span>61% selesai · 8 belum dieksekusi</span>
                            </div>
                        </div>

                        <!-- Test Run Item 2 -->
                        <div class="bg-[#0b0f19] border border-slate-800/80 rounded-xl p-4 space-y-3">
                            <div class="flex items-start justify-between">
                                <div>
                                    <div class="text-sm font-bold text-white">UAT Phase 1</div>
                                    <div class="text-[11px] text-slate-400">Mobile Banking App</div>
                                </div>
                                <span class="w-2 h-2 rounded-full bg-emerald-500 mt-1"></span>
                            </div>

                            <!-- Statistik Status Kecil -->
                            <div class="flex flex-wrap gap-2 text-[11px] font-bold">
                                <span class="text-emerald-400">10 Passed</span>
                                <span class="text-slate-600">•</span>
                                <span class="text-rose-400">6 Failed</span>
                                <span class="text-slate-600">•</span>
                                <span class="text-amber-400">1 Blocked</span>
                                <span class="text-slate-600">•</span>
                                <span class="text-slate-400">18 Untested</span>
                            </div>

                            <!-- Progress Bar Multiwarna -->
                            <div class="w-full h-2 bg-slate-800 rounded-full overflow-hidden flex">
                                <div class="bg-emerald-500 h-full" style="width: 30%"></div>
                                <div class="bg-rose-500 h-full" style="width: 15%"></div>
                                <div class="bg-amber-500 h-full" style="width: 5%"></div>
                            </div>

                            <div class="text-[10px] text-slate-400 flex justify-between">
                                <span>29% selesai · 18 belum dieksekusi</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- KANAN: BUG TERBARU -->
                <div class="bg-[#131b2e] border border-slate-800/80 rounded-2xl p-6 shadow-xl space-y-4">
                    <h3 class="text-sm font-bold text-white tracking-wide">Bug Terbaru</h3>
                    
                    <div class="space-y-3">
                        <!-- Bug 1 -->
                        <div class="bg-[#0b0f19] border border-slate-800/80 rounded-xl p-3.5 space-y-2">
                            <div class="flex items-start justify-between space-x-2">
                                <div class="flex items-start space-x-2">
                                    <span class="w-2 h-2 rounded-full bg-indigo-400 mt-1.5 shrink-0"></span>
                                    <span class="text-xs font-bold text-white leading-snug">Login gagal setelah 3x percobaan — halaman blank</span>
                                </div>
                                <div class="w-6 h-6 rounded-lg bg-indigo-600/20 text-indigo-400 font-bold text-[10px] flex items-center justify-center shrink-0 border border-indigo-500/30">DR</div>
                            </div>
                            <div class="flex items-center space-x-2 pt-1">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-500/10 text-blue-400 border border-blue-500/20">In Progress</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20 flex items-center space-x-0.5">
                                    <span>⚡ Critical</span>
                                </span>
                            </div>
                        </div>

                        <!-- Bug 2 -->
                        <div class="bg-[#0b0f19] border border-slate-800/80 rounded-xl p-3.5 space-y-2">
                            <div class="flex items-start justify-between space-x-2">
                                <div class="flex items-start space-x-2">
                                    <span class="w-2 h-2 rounded-full bg-rose-400 mt-1.5 shrink-0"></span>
                                    <span class="text-xs font-bold text-white leading-snug">Tombol keranjang tidak responsif di Safari</span>
                                </div>
                                <div class="w-6 h-6 rounded-lg bg-purple-600/20 text-purple-400 font-bold text-[10px] flex items-center justify-center shrink-0 border border-purple-500/30">EP</div>
                            </div>
                            <div class="flex items-center space-x-2 pt-1">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">Open</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20 flex items-center space-x-0.5">
                                    <span>↑ High</span>
                                </span>
                            </div>
                        </div>

                        <!-- Bug 3 -->
                        <div class="bg-[#0b0f19] border border-slate-800/80 rounded-xl p-3.5 space-y-2">
                            <div class="flex items-start justify-between space-x-2">
                                <div class="flex items-start space-x-2">
                                    <span class="w-2 h-2 rounded-full bg-indigo-400 mt-1.5 shrink-0"></span>
                                    <span class="text-xs font-bold text-white leading-snug">Total harga salah saat ada diskon bertingkat</span>
                                </div>
                                <div class="w-6 h-6 rounded-lg bg-indigo-600/20 text-indigo-400 font-bold text-[10px] flex items-center justify-center shrink-0 border border-indigo-500/30">DR</div>
                            </div>
                            <div class="flex items-center space-x-2 pt-1">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Resolved</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20 flex items-center space-x-0.5">
                                    <span>↑ High</span>
                                </span>
                            </div>
                        </div>

                        <!-- Bug 4 -->
                        <div class="bg-[#0b0f19] border border-slate-800/80 rounded-xl p-3.5 space-y-2">
                            <div class="flex items-start justify-between space-x-2">
                                <div class="flex items-start space-x-2">
                                    <span class="w-2 h-2 rounded-full bg-amber-400 mt-1.5 shrink-0"></span>
                                    <span class="text-xs font-bold text-white leading-snug">OTP tidak terkirim ke nomor Telkomsel</span>
                                </div>
                                <div class="w-6 h-6 rounded-lg bg-purple-600/20 text-purple-400 font-bold text-[10px] flex items-center justify-center shrink-0 border border-purple-500/30">EP</div>
                            </div>
                            <div class="flex items-center space-x-2 pt-1">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-orange-500/10 text-orange-400 border border-orange-500/20">Reopened</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20 flex items-center space-x-0.5">
                                    <span>⚡ Critical</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- BAGIAN BAWAH: RINGKASAN PROYEK (TABEL) -->
            <div class="bg-[#131b2e] border border-slate-800/80 rounded-2xl p-6 shadow-xl space-y-4">
                <h3 class="text-sm font-bold text-white tracking-wide">Ringkasan Proyek</h3>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-800 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                <th class="py-3 px-4">Proyek</th>
                                <th class="py-3 px-4">Test Cases</th>
                                <th class="py-3 px-4">Pass Rate</th>
                                <th class="py-3 px-4 text-right">Tim</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 text-xs">
                            <!-- Row 1 -->
                            <tr class="hover:bg-slate-800/30 transition">
                                <td class="py-4 px-4 flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-xl bg-indigo-600 font-bold text-white flex items-center justify-center text-xs shrink-0 shadow-md">E</div>
                                    <div>
                                        <div class="font-bold text-white">E-Commerce Platform</div>
                                        <div class="text-[11px] text-slate-400">Pengujian fitur belanja online & pembayaran digital</div>
                                    </div>
                                </td>
                                <td class="py-4 px-4 font-mono font-bold text-slate-300">48</td>
                                <td class="py-4 px-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-28 h-2 bg-slate-800 rounded-full overflow-hidden">
                                            <div class="bg-emerald-500 h-full" style="width: 87%"></div>
                                        </div>
                                        <span class="font-bold text-emerald-400 text-xs">87%</span>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-right">
                                    <div class="flex items-center justify-end space-x-1">
                                        <span class="w-6 h-6 rounded-lg bg-indigo-500/20 text-indigo-300 text-[9px] font-bold flex items-center justify-center border border-indigo-500/30">AP</span>
                                        <span class="w-6 h-6 rounded-lg bg-amber-500/20 text-amber-300 text-[9px] font-bold flex items-center justify-center border border-amber-500/30">SD</span>
                                        <span class="w-6 h-6 rounded-lg bg-blue-500/20 text-blue-300 text-[9px] font-bold flex items-center justify-center border border-blue-500/30">BS</span>
                                        <span class="w-6 h-6 rounded-lg bg-teal-500/20 text-teal-300 text-[9px] font-bold flex items-center justify-center border border-teal-500/30">CL</span>
                                        <span class="w-6 h-6 rounded-lg bg-purple-500/20 text-purple-300 text-[9px] font-bold flex items-center justify-center border border-purple-500/30">DR</span>
                                    </div>
                                </td>
                            </tr>

                            <!-- Row 2 -->
                            <tr class="hover:bg-slate-800/30 transition">
                                <td class="py-4 px-4 flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-xl bg-teal-600 font-bold text-white flex items-center justify-center text-xs shrink-0 shadow-md">M</div>
                                    <div>
                                        <div class="font-bold text-white">Mobile Banking App</div>
                                        <div class="text-[11px] text-slate-400">Pengujian aplikasi perbankan mobile iOS & Android</div>
                                    </div>
                                </td>
                                <td class="py-4 px-4 font-mono font-bold text-slate-300">72</td>
                                <td class="py-4 px-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-28 h-2 bg-slate-800 rounded-full overflow-hidden">
                                            <div class="bg-amber-500 h-full" style="width: 74%"></div>
                                        </div>
                                        <span class="font-bold text-amber-400 text-xs">74%</span>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-right">
                                    <div class="flex items-center justify-end space-x-1">
                                        <span class="w-6 h-6 rounded-lg bg-indigo-500/20 text-indigo-300 text-[9px] font-bold flex items-center justify-center border border-indigo-500/30">AP</span>
                                        <span class="w-6 h-6 rounded-lg bg-amber-500/20 text-amber-300 text-[9px] font-bold flex items-center justify-center border border-amber-500/30">SD</span>
                                        <span class="w-6 h-6 rounded-lg bg-blue-500/20 text-blue-300 text-[9px] font-bold flex items-center justify-center border border-blue-500/30">BS</span>
                                        <span class="w-6 h-6 rounded-lg bg-teal-500/20 text-teal-300 text-[9px] font-bold flex items-center justify-center border border-teal-500/30">CL</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>
<x-profile-modal />
</body>
</html>