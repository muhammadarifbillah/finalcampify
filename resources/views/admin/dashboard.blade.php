@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
    <div class="space-y-6 pb-12">
        {{-- Header --}}
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-[32px] font-black text-slate-800 tracking-tight leading-none">Dashboard Utama</h1>
                <p class="text-slate-500 font-medium mt-2">Monitoring transaksi, user, seller, dan aktivitas marketplace
                    harian.</p>
            </div>
            <div class="relative" id="exportDropdownContainer">
                <button onclick="toggleExportDropdown()" id="exportDropdownBtn"
                    class="inline-flex items-center gap-2 bg-[#065f46] hover:bg-[#064e3b] text-white px-6 py-3 rounded-lg font-black transition-all shadow-sm">
                    <i data-lucide="download" class="w-5 h-5"></i>
                    Export Laporan
                    <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-200" id="exportChevron"></i>
                </button>

                {{-- Export Dropdown Panel --}}
                <div id="exportDropdownPanel"
                    class="hidden absolute right-0 top-full mt-2 w-[420px] bg-white border border-slate-200 rounded-2xl shadow-2xl z-50 overflow-hidden animate-fadeIn">
                    {{-- Header --}}
                    <div class="bg-gradient-to-r from-[#065f46] to-[#064e3b] px-6 py-4">
                        <h3 class="text-white font-black text-sm tracking-wide">📊 Pilih Jenis Laporan</h3>
                        <p class="text-emerald-200 text-[11px] font-medium mt-0.5">Data akan diunduh dalam format PDF siap
                            cetak</p>
                    </div>

                    {{-- Date Range Filter --}}
                    <div class="px-6 pt-4 pb-3 border-b border-slate-100 bg-slate-50/50">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Filter Tanggal
                            (Opsional)</p>
                        <div class="flex gap-2">
                            <div class="flex-1">
                                <label class="text-[10px] font-bold text-slate-500 block mb-1">Dari</label>
                                <input type="date" id="exportDateFrom"
                                    class="w-full text-xs px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all font-semibold text-slate-600">
                            </div>
                            <div class="flex-1">
                                <label class="text-[10px] font-bold text-slate-500 block mb-1">Sampai</label>
                                <input type="date" id="exportDateTo"
                                    class="w-full text-xs px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all font-semibold text-slate-600">
                            </div>
                        </div>
                    </div>

                    {{-- Report Type Options --}}
                    <div class="p-3 space-y-1">
                        <a href="#" onclick="doExport('summary')"
                            class="export-option flex items-center gap-4 p-3 rounded-xl hover:bg-[#ecfdf5] transition-all group">
                            <div
                                class="w-10 h-10 rounded-xl bg-[#ecfdf5] text-[#059669] flex items-center justify-center flex-shrink-0 group-hover:bg-[#059669] group-hover:text-white transition-colors duration-200">
                                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                            </div>
                            <div class="flex-1">
                                <p
                                    class="text-sm font-extrabold text-slate-700 group-hover:text-[#065f46] transition-colors">
                                    Ringkasan Dashboard</p>
                                <p class="text-[10px] font-bold text-slate-400">Semua metrik, statistik, dan tren bulanan
                                </p>
                            </div>
                            <i data-lucide="download"
                                class="w-4 h-4 text-slate-300 group-hover:text-[#059669] transition-colors"></i>
                        </a>

                        <a href="#" onclick="doExport('orders')"
                            class="export-option flex items-center gap-4 p-3 rounded-xl hover:bg-[#ecfdf5] transition-all group">
                            <div
                                class="w-10 h-10 rounded-xl bg-[#f0fdfa] text-[#0f766e] flex items-center justify-center flex-shrink-0 group-hover:bg-[#0f766e] group-hover:text-white transition-colors duration-200">
                                <i data-lucide="shopping-bag" class="w-5 h-5"></i>
                            </div>
                            <div class="flex-1">
                                <p
                                    class="text-sm font-extrabold text-slate-700 group-hover:text-[#065f46] transition-colors">
                                    Data Transaksi / Orders</p>
                                <p class="text-[10px] font-bold text-slate-400">Detail order, pembeli, produk, status, kurir
                                </p>
                            </div>
                            <i data-lucide="download"
                                class="w-4 h-4 text-slate-300 group-hover:text-[#059669] transition-colors"></i>
                        </a>

                        <a href="#" onclick="doExport('returns')"
                            class="export-option flex items-center gap-4 p-3 rounded-xl hover:bg-[#ecfdf5] transition-all group">
                            <div
                                class="w-10 h-10 rounded-xl bg-[#fef2f2] text-[#dc2626] flex items-center justify-center flex-shrink-0 group-hover:bg-[#dc2626] group-hover:text-white transition-colors duration-200">
                                <i data-lucide="undo-2" class="w-5 h-5"></i>
                            </div>
                            <div class="flex-1">
                                <p
                                    class="text-sm font-extrabold text-slate-700 group-hover:text-[#065f46] transition-colors">
                                    Data Retur & Escrow</p>
                                <p class="text-[10px] font-bold text-slate-400">Deposit, denda, refund, status keterlambatan
                                </p>
                            </div>
                            <i data-lucide="download"
                                class="w-4 h-4 text-slate-300 group-hover:text-[#059669] transition-colors"></i>
                        </a>

                        <a href="#" onclick="doExport('users')"
                            class="export-option flex items-center gap-4 p-3 rounded-xl hover:bg-[#ecfdf5] transition-all group">
                            <div
                                class="w-10 h-10 rounded-xl bg-[#f0f9ff] text-[#0284c7] flex items-center justify-center flex-shrink-0 group-hover:bg-[#0284c7] group-hover:text-white transition-colors duration-200">
                                <i data-lucide="users" class="w-5 h-5"></i>
                            </div>
                            <div class="flex-1">
                                <p
                                    class="text-sm font-extrabold text-slate-700 group-hover:text-[#065f46] transition-colors">
                                    Data Pengguna</p>
                                <p class="text-[10px] font-bold text-slate-400">Buyer, seller, KYC, bank, jumlah order</p>
                            </div>
                            <i data-lucide="download"
                                class="w-4 h-4 text-slate-300 group-hover:text-[#059669] transition-colors"></i>
                        </a>

                        <a href="#" onclick="doExport('products')"
                            class="export-option flex items-center gap-4 p-3 rounded-xl hover:bg-[#ecfdf5] transition-all group">
                            <div
                                class="w-10 h-10 rounded-xl bg-[#fffbeb] text-[#d97706] flex items-center justify-center flex-shrink-0 group-hover:bg-[#d97706] group-hover:text-white transition-colors duration-200">
                                <i data-lucide="package" class="w-5 h-5"></i>
                            </div>
                            <div class="flex-1">
                                <p
                                    class="text-sm font-extrabold text-slate-700 group-hover:text-[#065f46] transition-colors">
                                    Data Produk</p>
                                <p class="text-[10px] font-bold text-slate-400">Katalog, harga, stok, toko, kategori</p>
                            </div>
                            <i data-lucide="download"
                                class="w-4 h-4 text-slate-300 group-hover:text-[#059669] transition-colors"></i>
                        </a>
                    </div>

                    {{-- Footer --}}
                    <div class="px-6 py-3 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                        <p class="text-[10px] font-bold text-slate-400"><i data-lucide="info"
                                class="w-3 h-3 inline-block mr-1"></i>Filter tanggal berlaku untuk Orders & Returns</p>
                        <button onclick="toggleExportDropdown()"
                            class="text-[10px] font-black text-slate-500 hover:text-slate-700 uppercase tracking-widest">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 1: Primary Stats --}}
        <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-4">
            {{-- Card 1: Pengguna --}}
            <div
                class="premium-glass-card premium-glass-card-emerald p-6 flex flex-col justify-between min-h-[220px] group">
                <div class="absolute top-0 left-0 w-full h-[4px] bg-[#059669]"></div>
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">TOTAL PENGGUNA</p>
                        <div
                            class="w-8 h-8 rounded-lg bg-[#ecfdf5] text-[#059669] flex items-center justify-center transition-colors group-hover:bg-[#059669] group-hover:text-white">
                            <i data-lucide="users" class="w-4 h-4"></i>
                        </div>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <h2 class="text-4xl font-extrabold text-slate-800 tracking-tight">{{ $users }}</h2>
                        <span class="text-xs font-semibold text-slate-400">Akun Terdaftar</span>
                    </div>
                </div>
                <div class="mt-5 space-y-2 border-t border-slate-100 pt-4">
                    <div class="flex justify-between text-[11px] font-bold text-slate-500 uppercase tracking-tight">
                        <span class="flex items-center gap-1.5"><span
                                class="w-1.5 h-1.5 rounded-full bg-[#059669]"></span>Pembeli (Buyer):</span>
                        <span class="text-slate-800 font-extrabold">{{ $buyers }} Akun</span>
                    </div>
                    <div class="flex justify-between text-[11px] font-bold text-slate-500 uppercase tracking-tight">
                        <span class="flex items-center gap-1.5"><span
                                class="w-1.5 h-1.5 rounded-full bg-[#10b981]"></span>Penjual (Seller):</span>
                        <span class="text-[#059669] font-extrabold">{{ $sellers }} Akun</span>
                    </div>
                </div>
            </div>

            {{-- Card 2: Produk --}}
            <div class="premium-glass-card premium-glass-card-forest p-6 flex flex-col justify-between min-h-[220px] group">
                <div class="absolute top-0 left-0 w-full h-[4px] bg-[#064e3b]"></div>
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">TOTAL PRODUK</p>
                        <div
                            class="w-8 h-8 rounded-lg bg-[#e6f4ea] text-[#047857] flex items-center justify-center transition-colors group-hover:bg-[#064e3b] group-hover:text-white">
                            <i data-lucide="package" class="w-4 h-4"></i>
                        </div>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <h2 class="text-4xl font-extrabold text-slate-800 tracking-tight">{{ $products }}</h2>
                        <span class="text-xs font-semibold text-slate-400">Unit Katalog</span>
                    </div>
                </div>
                <div class="mt-5 border-t border-slate-100 pt-4 flex items-center gap-2">
                    <span class="flex h-2 w-2 relative">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-[#059669]"></span>
                    </span>
                    <p class="text-xs font-extrabold text-[#059669]">+{{ $newProductsThisWeek }} Baru Minggu Ini</p>
                </div>
            </div>

            {{-- Card 3: Transaksi --}}
            <div class="premium-glass-card premium-glass-card-teal p-6 flex flex-col justify-between min-h-[220px] group">
                <div class="absolute top-0 left-0 w-full h-[4px] bg-[#0f766e]"></div>
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">TOTAL TRANSAKSI</p>
                        <div
                            class="w-8 h-8 rounded-lg bg-[#f0fdfa] text-[#0f766e] flex items-center justify-center transition-colors group-hover:bg-[#0f766e] group-hover:text-white">
                            <i data-lucide="banknote" class="w-4 h-4"></i>
                        </div>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <h2 class="text-4xl font-extrabold text-slate-800 tracking-tight">{{ $transactions }}</h2>
                        <span class="text-xs font-semibold text-slate-400">Penyewaan & Pembelian</span>
                    </div>
                </div>
                <div class="mt-5 space-y-2 border-t border-slate-100 pt-4">
                    <div class="flex justify-between text-[11px] font-bold text-slate-500 uppercase tracking-tight">
                        <span class="flex items-center gap-1.5"><span
                                class="w-1.5 h-1.5 rounded-full bg-[#059669]"></span>Penyewaan:</span>
                        <span class="text-[#059669] font-extrabold">{{ $rentalCount }} Transaksi</span>
                    </div>
                    <div class="flex justify-between text-[11px] font-bold text-slate-500 uppercase tracking-tight">
                        <span class="flex items-center gap-1.5"><span
                                class="w-1.5 h-1.5 rounded-full bg-[#0f766e]"></span>Pembelian:</span>
                        <span class="text-[#0f766e] font-extrabold">{{ $buyCount }} Transaksi</span>
                    </div>
                </div>
            </div>

            {{-- Card 4: Escrow --}}
            <div class="premium-glass-card premium-glass-card-mint p-6 flex flex-col justify-between min-h-[220px] group">
                <div class="absolute top-0 left-0 w-full h-[4px] bg-[#10b981]"></div>
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">TOTAL ESCROW TERTAHAN</p>
                        <div class="flex items-center gap-2">
                            <span
                                class="bg-[#059669] text-[8px] font-black text-white px-2 py-0.5 rounded shadow-sm tracking-wider">NEW</span>
                            <div
                                class="w-8 h-8 rounded-lg bg-[#f0fdf4] text-[#10b981] flex items-center justify-center transition-colors group-hover:bg-[#10b981] group-hover:text-white">
                                <i data-lucide="wallet-cards" class="w-4 h-4"></i>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h2 class="text-3xl font-extrabold text-[#064e3b] tracking-tight">Rp
                            {{ number_format($totalEscrow, 0, ',', '.') }}</h2>
                    </div>
                </div>
                <div class="mt-5 border-t border-slate-100 pt-4 flex items-center gap-2">
                    <span class="flex h-2 w-2 relative">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-[#059669]"></span>
                    </span>
                    <p class="text-xs font-extrabold text-[#059669]">Status Aktif</p>
                </div>
            </div>
        </div>

        {{-- Row 2: Secondary Metrics --}}
        <div class="flex gap-4 overflow-x-auto pb-3 flex-nowrap custom-scrollbar">
            <!-- Card 2.1: Jaminan Sewa -->
            <div
                class="premium-glass-card premium-glass-card-emerald p-5 flex items-center gap-4 min-w-[280px] flex-shrink-0 flex-1 group">
                <div
                    class="p-3 bg-[#f0fdf4] text-[#059669] rounded-xl group-hover:bg-[#059669] group-hover:text-white transition-colors duration-300 flex-shrink-0">
                    <i data-lucide="wallet" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1.5">Jaminan
                        Sewa (Escrow)</p>
                    <p class="text-base font-extrabold text-slate-800 leading-none">Rp
                        {{ number_format($jaminanSewaEscrow, 0, ',', '.') }}</p>
                    <p class="text-[9px] text-slate-400 font-bold mt-1">Uang jaminan sewa aktif di platform</p>
                </div>
            </div>

            <!-- Card 2.3: Rata-rata Durasi -->
            <div
                class="premium-glass-card premium-glass-card-emerald p-5 flex items-center gap-4 min-w-[280px] flex-shrink-0 flex-1 group">
                <div
                    class="p-3 bg-[#f0fdf4] text-[#059669] rounded-xl group-hover:bg-[#059669] group-hover:text-white transition-colors duration-300 flex-shrink-0">
                    <i data-lucide="timer" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1.5">Rata-rata
                        Durasi Penyelesaian</p>
                    <p class="text-base font-extrabold text-slate-800 leading-none">{{ $avgResolutionTime }} Hari</p>
                    <p class="text-[9px] text-slate-400 font-bold mt-1">Kecepatan rata-rata penyelesaian retur sewa</p>
                </div>
            </div>

            <!-- Card 2.4: Pendapatan Admin -->
            <div
                class="premium-glass-card premium-glass-card-emerald p-5 flex items-center gap-4 min-w-[280px] flex-shrink-0 flex-1 group">
                <div
                    class="p-3 bg-[#f0fdf4] text-[#059669] rounded-xl group-hover:bg-[#059669] group-hover:text-white transition-colors duration-300 flex-shrink-0">
                    <i data-lucide="trending-up" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1.5">
                        Pendapatan Admin (Fee)</p>
                    <p class="text-base font-extrabold text-slate-800 leading-none">Rp
                        {{ number_format($adminRentalRevenue, 0, ',', '.') }}</p>
                    <p class="text-[9px] text-slate-400 font-bold mt-1">Dipotong 10% dari setiap biaya sewa</p>
                </div>
            </div>

            <!-- Card 2.5: Denda Keterlambatan -->
            <div
                class="premium-glass-card premium-glass-card-emerald p-5 flex items-center gap-4 min-w-[280px] flex-shrink-0 flex-1 group">
                <div
                    class="p-3 bg-[#f0fdf4] text-[#059669] rounded-xl group-hover:bg-[#059669] group-hover:text-white transition-colors duration-300 flex-shrink-0">
                    <i data-lucide="shield-alert" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1.5">Denda
                        Keterlambatan</p>
                    <p class="text-base font-extrabold text-slate-800 leading-none">Rp
                        {{ number_format($totalLateFees, 0, ',', '.') }}</p>
                    <p class="text-[9px] text-slate-400 font-bold mt-1">Akumulasi denda dari pengembalian telat</p>
                </div>
            </div>

            <!-- Card 2.6: Disalurkan ke Toko -->
            <div
                class="premium-glass-card premium-glass-card-emerald p-5 flex items-center gap-4 min-w-[280px] flex-shrink-0 flex-1 group">
                <div
                    class="p-3 bg-[#f0fdf4] text-[#059669] rounded-xl group-hover:bg-[#059669] group-hover:text-white transition-colors duration-300 flex-shrink-0">
                    <i data-lucide="credit-card" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1.5">
                        Disalurkan ke Toko</p>
                    <p class="text-base font-extrabold text-slate-800 leading-none">Rp
                        {{ number_format($totalReturnToSeller, 0, ',', '.') }}</p>
                    <p class="text-[9px] text-slate-400 font-bold mt-1">Total dana retur yang harus dicairkan ke seller</p>
                </div>
            </div>
        </div>

        {{-- Row 3: Alerts --}}
        <div class="grid gap-4 grid-cols-1 md:grid-cols-2">
            <!-- Card 1: Overdue Warning -->
            <div
                class="bg-gradient-to-br from-[#064e3b] to-[#043024] text-white p-5 rounded-2xl flex items-center gap-5 shadow-sm border border-[#064e3b]/20 transition hover:shadow-md relative overflow-hidden group">
                <div
                    class="absolute -right-6 -bottom-6 w-24 h-24 bg-white/5 rounded-full blur-xl group-hover:scale-125 transition-transform duration-500">
                </div>
                <div class="p-3 bg-white/10 rounded-xl backdrop-blur-sm flex-shrink-0"><i data-lucide="triangle-alert"
                        class="w-6 h-6 text-red-300 animate-pulse"></i></div>
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span
                            class="text-[9px] font-black tracking-widest text-red-200 uppercase bg-[#991b1b]/80 px-2 py-0.5 rounded-full border border-red-800/30">OVERDUE
                            WARNING</span>
                    </div>
                    <p class="text-sm font-extrabold uppercase leading-tight">Ada {{ $overdueReturns }} UNIT SEWA MELEBIHI
                        BATAS (OVERDUE)</p>
                </div>
            </div>

            <!-- Card 2: Today's Due Warning -->
            <div
                class="bg-gradient-to-br from-[#0f766e] to-[#115e59] text-white p-5 rounded-2xl flex items-center gap-5 shadow-sm border border-[#0f766e]/20 transition hover:shadow-md relative overflow-hidden group">
                <div
                    class="absolute -right-6 -bottom-6 w-24 h-24 bg-white/5 rounded-full blur-xl group-hover:scale-125 transition-transform duration-500">
                </div>
                <div class="p-3 bg-white/10 rounded-xl backdrop-blur-sm flex-shrink-0"><i data-lucide="calendar-clock"
                        class="w-6 h-6 text-teal-100"></i></div>
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span
                            class="text-[9px] font-black tracking-widest text-teal-200 uppercase bg-[#134e4a]/80 px-2 py-0.5 rounded-full border border-teal-800/30">JADWAL
                            HARI INI</span>
                    </div>
                    <p class="text-sm font-extrabold uppercase leading-tight">Ada {{ $todayDueRentals }} unit sewa jatuh
                        tempo hari ini</p>
                </div>
            </div>

            <!-- Card 3: Today's Due Report -->
            <div
                class="bg-gradient-to-br from-[#059669] to-[#047857] text-white p-5 rounded-2xl flex items-center gap-5 shadow-sm border border-emerald-500/20 transition hover:shadow-md relative overflow-hidden group">
                <div
                    class="absolute -right-6 -bottom-6 w-24 h-24 bg-white/5 rounded-full blur-xl group-hover:scale-125 transition-transform duration-500">
                </div>
                <div class="p-3 bg-white/10 rounded-xl backdrop-blur-sm flex-shrink-0"><i data-lucide="info"
                        class="w-6 h-6 text-emerald-100"></i></div>
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span
                            class="text-[9px] font-black tracking-widest text-emerald-100 uppercase bg-[#064e3b]/80 px-2 py-0.5 rounded-full border border-emerald-500/20">LAPORAN
                            AKTIVITAS</span>
                    </div>
                    <p class="text-sm font-extrabold uppercase leading-tight">Ada {{ $todayDueRentals }} unit sewa jatuh
                        tempo hari ini</p>
                </div>
            </div>

            <!-- Card 4: Pending Returns Check -->
            <div
                class="bg-gradient-to-br from-[#b45309] to-[#78350f] text-white p-5 rounded-2xl flex items-center gap-5 shadow-sm border border-orange-500/20 transition hover:shadow-md relative overflow-hidden group">
                <div
                    class="absolute -right-6 -bottom-6 w-24 h-24 bg-white/5 rounded-full blur-xl group-hover:scale-125 transition-transform duration-500">
                </div>
                <div class="p-3 bg-white/10 rounded-xl backdrop-blur-sm flex-shrink-0"><i data-lucide="package-search"
                        class="w-6 h-6 text-orange-100"></i></div>
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span
                            class="text-[9px] font-black tracking-widest text-orange-100 uppercase bg-[#78350f]/80 px-2 py-0.5 rounded-full border border-orange-500/20">PENGECEKAN
                            RETUR</span>
                    </div>
                    <p class="text-sm font-extrabold uppercase leading-tight">Ada {{ $pendingReturns }} pengajuan retur
                        (pending) baru</p>
                </div>
            </div>
        </div>

        {{-- Row 4: Status Cards --}}
        <div class="grid gap-4 grid-cols-2 xl:grid-cols-4">
            <!-- Card 4.1: PRODUK WAITING -->
            <div class="premium-glass-card premium-glass-card-emerald p-5 flex items-center gap-4 group">
                <div
                    class="w-10 h-10 rounded-xl bg-[#e6f4ec] text-[#047857] flex items-center justify-center flex-shrink-0 group-hover:bg-[#047857] group-hover:text-white transition-colors duration-300">
                    <i data-lucide="package" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-[#047857] uppercase tracking-widest leading-none mb-1">PRODUK
                        WAITING</p>
                    <p class="text-xs text-[#065f46] font-bold opacity-80">{{ $pendingProducts }} produk menunggu validasi
                    </p>
                </div>
            </div>

            <!-- Card 4.2: TOKO BANNED -->
            <div class="premium-glass-card premium-glass-card-forest p-5 flex items-center gap-4 group">
                <div
                    class="w-10 h-10 rounded-xl bg-[#e2ebd5] text-[#4a5f54] flex items-center justify-center flex-shrink-0 group-hover:bg-[#4a5f54] group-hover:text-white transition-colors duration-300">
                    <i data-lucide="ban" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-800 uppercase tracking-widest leading-none mb-1">TOKO BANNED
                    </p>
                    <p class="text-xs text-slate-500 font-bold opacity-80">{{ $bannedStores }} toko sedang diblokir</p>
                </div>
            </div>

            <!-- Card 4.3: CHAT BERMASALAH -->
            <div class="premium-glass-card premium-glass-card-teal p-5 flex items-center gap-4 group">
                <div
                    class="w-10 h-10 rounded-xl bg-[#e2eedc] text-[#0b663b] flex items-center justify-center flex-shrink-0 group-hover:bg-[#0b663b] group-hover:text-white transition-colors duration-300">
                    <i data-lucide="message-square" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-[#0b663b] uppercase tracking-widest leading-none mb-1">CHAT
                        BERMASALAH</p>
                    <p class="text-xs text-[#064e3b] font-bold opacity-80">{{ $flaggedChats }} chat ditandai sistem</p>
                </div>
            </div>

            <!-- Card 4.4: PENDING KYC -->
            <div class="premium-glass-card premium-glass-card-mint p-5 flex items-center gap-4 group">
                <div
                    class="w-10 h-10 rounded-xl bg-[#dcfce7] text-[#15803d] flex items-center justify-center flex-shrink-0 group-hover:bg-[#15803d] group-hover:text-white transition-colors duration-300">
                    <i data-lucide="user-check" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-[#15803d] uppercase tracking-widest leading-none mb-1">PENDING KYC
                    </p>
                    <p class="text-xs text-[#166534] font-bold opacity-80">{{ $pendingKyc }} user menunggu verifikasi</p>
                </div>
            </div>
        </div>

        {{-- Row 5: Main Content --}}
        <div class="grid gap-6 xl:grid-cols-[1fr_360px]">
            {{-- Table --}}
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden flex flex-col">
                <div class="flex items-center gap-8 px-8 py-5 border-b border-slate-50">
                    <a href="?filter=all"
                        class="{{ $filter === 'all' || !$filter ? 'bg-[#064e3b] text-white px-6 py-2 rounded-lg' : 'text-slate-400 hover:text-slate-600' }} text-sm font-black uppercase tracking-tight transition-colors">Semua
                        Masalah</a>
                    <a href="?filter=overdue"
                        class="{{ $filter === 'overdue' ? 'bg-[#064e3b] text-white px-6 py-2 rounded-lg' : 'text-slate-400 hover:text-slate-600' }} text-sm font-black uppercase tracking-tight transition-colors">Sewa
                        Terlambat (Overdue)</a>
                    <a href="{{ route('admin.returns.sewa') }}"
                        class="ml-auto text-sm font-black text-[#059669] hover:underline">Lihat Semua</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50/50 border-b border-slate-100">
                            <tr>
                                <th class="px-8 py-4 text-[11px] font-black uppercase text-slate-400 tracking-widest">ID
                                    SEWA</th>
                                <th class="px-8 py-4 text-[11px] font-black uppercase text-slate-400 tracking-widest">PRODUK
                                </th>
                                <th class="px-8 py-4 text-[11px] font-black uppercase text-slate-400 tracking-widest">
                                    PENYEWA/PEMBELI</th>
                                <th class="px-8 py-4 text-[11px] font-black uppercase text-slate-400 tracking-widest">TGL
                                    KEMBALI/DEADLINE</th>
                                <th
                                    class="px-8 py-4 text-[11px] font-black uppercase text-slate-400 tracking-widest text-center">
                                    DURASI LATE</th>
                                <th
                                    class="px-8 py-4 text-[11px] font-black uppercase text-slate-400 tracking-widest text-right">
                                    TOTAL ESCROW</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($allIssues as $issue)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-8 py-6 font-black text-[#059669]">#RT-{{ $issue->id }}</td>
                                    <td class="px-8 py-6 font-bold text-slate-700 text-xs truncate max-w-[200px]">
                                        {{ $issue->order->details->first()->product->name ?? 'Produk' }}</td>
                                    <td class="px-8 py-6 font-black text-slate-600 text-xs">
                                        {{ $issue->order->buyer->name ?? 'User' }}</td>
                                    <td class="px-8 py-6">
                                        @if($issue->expected_date)
                                            <div class="text-[12px] font-black text-[#dc2626]">
                                                {{ $issue->expected_date->format('d M Y, H:i') }}</div>
                                            @if($issue->expected_date->isPast())
                                                <div
                                                    class="text-[9px] font-black uppercase text-[#991b1b] flex items-center gap-1 mt-1">
                                                    <div class="w-1.5 h-1.5 bg-[#dc2626] rounded-full"></div> TERLAMBAT
                                                    {{ round($issue->expected_date->diffInHours(now())) }} JAM
                                                </div>
                                            @endif
                                        @else
                                            <div class="text-[12px] font-black text-slate-500">-</div>
                                        @endif
                                    </td>
                                    <td class="px-8 py-6 text-center text-xs font-black text-slate-600">
                                        {{ $issue->expected_date ? round($issue->expected_date->diffInDays(now())) . ' Hari' : '-' }}
                                    </td>
                                    <td class="px-8 py-6 text-right font-black text-slate-800 text-sm">
                                        <span
                                            class="text-[#b91c1c] mr-1">Rp</span>{{ number_format($issue->deposit_amount + $issue->rental_fee_amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Activity --}}
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-8 flex flex-col relative">
                <h3 class="text-xl font-black text-slate-800 mb-10">Umpan Aktivitas Terbaru</h3>
                <div
                    class="flex-1 space-y-10 relative before:absolute before:left-[19px] before:top-2 before:bottom-2 before:w-px before:bg-slate-100">
                    @foreach($activityFeed as $act)
                        <div class="relative" style="padding-left: 3.5rem;">
                            <div
                                class="absolute left-0 top-0 w-10 h-10 rounded-full border-4 border-white shadow flex items-center justify-center
                                    {{ $act['type'] == 'return' ? 'bg-[#fef2f2] text-[#ef4444]' : ($act['type'] == 'report' ? 'bg-[#fffbeb] text-[#f59e0b]' : 'bg-[#ecfdf5] text-[#10b981]') }}">
                                @if($act['type'] == 'return')
                                    <i data-lucide="triangle-alert" class="w-4 h-4"></i>
                                @elseif($act['type'] == 'report')
                                    <i data-lucide="flag" class="w-4 h-4"></i>
                                @else
                                    <i data-lucide="check" class="w-4 h-4"></i>
                                @endif
                            </div>
                            <div>
                                <p class="text-[14px] font-bold text-slate-700 leading-tight">{{ $act['title'] }}</p>
                                <p class="text-xs font-black text-[#059669] mt-1">{{ $act['meta'] }}</p>
                                <p class="text-[11px] font-bold text-slate-400 mt-2 italic">{{ $act['time']->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Row 6: Bottom Widgets (Charts) --}}
        <div class="grid gap-6 md:grid-cols-2">
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-8">
                <div class="mb-6">
                    <h3 class="text-lg font-black text-slate-800 tracking-tight">Tren Pendapatan Bulanan</h3>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-1">Total nilai transaksi
                        (Rp) per bulan</p>
                </div>
                <div id="revenueChart" class="h-[280px]"></div>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-8">
                <div class="mb-6">
                    <h3 class="text-lg font-black text-slate-800 tracking-tight">Aktivitas Platform Bulanan</h3>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-1">Perbandingan jumlah
                        transaksi vs pendaftar baru</p>
                </div>
                <div id="activityChart" class="h-[280px]"></div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];

            // Data dari backend (diisi per 1-12 bulan, kita gunakan index 0-11)
            const rawRevenue = @json($monthlyRevenue ?? []);
            const rawTransactions = @json($monthlyTransactionCounts ?? []);
            const rawUsers = @json($monthlyUserActivity ?? []);

            const revenueData = rawRevenue.slice(0, 12);
            const transactionData = rawTransactions.slice(0, 12);
            const userData = rawUsers.slice(0, 12);

            // Chart 1: Revenue (Area)
            const revenueOptions = {
                series: [{
                    name: 'Pendapatan',
                    data: revenueData
                }],
                chart: {
                    type: 'area',
                    height: 280,
                    toolbar: { show: false },
                    fontFamily: 'inherit'
                },
                colors: ['#059669'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.4,
                        opacityTo: 0,
                        stops: [0, 90, 100]
                    }
                },
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 3 },
                xaxis: {
                    categories: months,
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: {
                    labels: {
                        formatter: function (value) {
                            if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                            if (value >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + 'K';
                            return 'Rp ' + value;
                        }
                    }
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return "Rp " + val.toLocaleString('id-ID');
                        }
                    }
                }
            };

            const revenueChart = new ApexCharts(document.querySelector("#revenueChart"), revenueOptions);
            revenueChart.render();

            // Chart 2: Activity (Bar)
            const activityOptions = {
                series: [{
                    name: 'Transaksi Baru',
                    data: transactionData
                }, {
                    name: 'Pengguna Baru',
                    data: userData
                }],
                chart: {
                    type: 'bar',
                    height: 280,
                    toolbar: { show: false },
                    fontFamily: 'inherit'
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        borderRadius: 4
                    },
                },
                colors: ['#059669', '#a7f3d0'],
                dataLabels: { enabled: false },
                stroke: { show: true, width: 2, colors: ['transparent'] },
                xaxis: { categories: months },
                yaxis: {
                    title: { text: 'Jumlah', style: { fontWeight: 800, color: '#94a3b8' } }
                },
                fill: { opacity: 1 },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val + " Data";
                        }
                    }
                }
            };

            const activityChart = new ApexCharts(document.querySelector("#activityChart"), activityOptions);
            activityChart.render();
        });

        // ===== Export Report Dropdown =====
        function toggleExportDropdown() {
            const panel = document.getElementById('exportDropdownPanel');
            const chevron = document.getElementById('exportChevron');

            if (panel.classList.contains('hidden')) {
                panel.classList.remove('hidden');
                panel.style.animation = 'fadeInDown 0.2s ease-out';
                chevron.style.transform = 'rotate(180deg)';
            } else {
                panel.classList.add('hidden');
                chevron.style.transform = 'rotate(0deg)';
            }
        }

        function doExport(type) {
            event.preventDefault();

            let url = '{{ route("admin.dashboard.export") }}?type=' + type;

            const from = document.getElementById('exportDateFrom').value;
            const to = document.getElementById('exportDateTo').value;

            if (from && to) {
                url += '&from=' + from + '&to=' + to;
            }

            // Trigger download
            window.location.href = url;

            // Close dropdown after short delay
            setTimeout(() => {
                toggleExportDropdown();
            }, 300);
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function (e) {
            const container = document.getElementById('exportDropdownContainer');
            const panel = document.getElementById('exportDropdownPanel');

            if (container && panel && !container.contains(e.target) && !panel.classList.contains('hidden')) {
                panel.classList.add('hidden');
                document.getElementById('exportChevron').style.transform = 'rotate(0deg)';
            }
        });
    </script>

    <style>
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endsection