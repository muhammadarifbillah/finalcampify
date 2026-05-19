@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
    <div class="space-y-6 pb-12">
        {{-- Header --}}
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-[32px] font-black text-slate-800 tracking-tight leading-none">Dashboard Utama</h1>
                <p class="text-slate-500 font-medium mt-2">Monitoring transaksi, user, seller, dan aktivitas marketplace harian.</p>
            </div>
            <a href="/admin/orders" class="inline-flex items-center gap-2 bg-[#065f46] hover:bg-[#064e3b] text-white px-6 py-3 rounded-lg font-black transition-all shadow-sm">
                <i data-lucide="download" class="w-5 h-5"></i>
                Export Report
            </a>
        </div>

        {{-- Row 1: Primary Stats --}}
        <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-4">
            {{-- Card 1: Pengguna --}}
            <div class="bg-white border border-slate-200 rounded-xl p-8 shadow-sm flex flex-col justify-between h-full min-h-[200px]">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <p class="text-[11px] font-black uppercase tracking-widest text-slate-400">TOTAL PENGGUNA</p>
                        <div class="p-1.5 bg-[#ecfdf5] rounded text-[#059669]">
                            <i data-lucide="users" class="w-4 h-4"></i>
                        </div>
                    </div>
                    <h2 class="text-[48px] font-black text-slate-800 leading-none tracking-tighter">{{ $users }}</h2>
                </div>
                <div class="mt-6 space-y-1 border-t border-slate-100 pt-4">
                    <div class="flex justify-between text-[11px] font-bold text-slate-500 uppercase tracking-tight">
                        <span>Pembeli (Buyer):</span>
                        <span class="text-slate-800 font-black">{{ $buyers }} Akun</span>
                    </div>
                    <div class="flex justify-between text-[11px] font-bold text-slate-500 uppercase tracking-tight">
                        <span>Penjual (Seller):</span>
                        <span class="text-[#059669] font-black">{{ $sellers }} Akun</span>
                    </div>
                </div>
            </div>

            {{-- Card 2: Produk --}}
            <div class="bg-white border border-slate-200 rounded-xl p-8 shadow-sm flex flex-col justify-between h-full min-h-[200px]">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <p class="text-[11px] font-black uppercase tracking-widest text-slate-400">TOTAL PRODUK</p>
                        <div class="p-1.5 bg-[#fef2f2] rounded text-[#dc2626]">
                            <i data-lucide="calendar" class="w-4 h-4"></i>
                        </div>
                    </div>
                    <h2 class="text-[48px] font-black text-slate-800 leading-none tracking-tighter">{{ $products }}</h2>
                </div>
                <p class="text-xs font-black text-[#059669] mt-3">+{{ $newProductsThisWeek }} Baru Minggu Ini</p>
            </div>

            {{-- Card 3: Transaksi --}}
            <div class="bg-white border border-slate-200 rounded-xl p-8 shadow-sm flex flex-col justify-between h-full min-h-[200px]">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <p class="text-[11px] font-black uppercase tracking-widest text-slate-400">TOTAL TRANSAKSI</p>
                        <div class="p-1.5 bg-[#eff6ff] rounded text-[#2563eb]">
                            <i data-lucide="banknote" class="w-4 h-4"></i>
                        </div>
                    </div>
                    <h2 class="text-[48px] font-black text-slate-800 leading-none tracking-tighter">{{ $transactions }}</h2>
                </div>
                <div class="mt-6 space-y-1 border-t border-slate-100 pt-4">
                    <div class="flex justify-between text-[11px] font-bold text-slate-500 uppercase tracking-tight">
                        <span>Penyewaan:</span>
                        <span class="text-[#059669] font-black">{{ $rentalCount }} Transaksi</span>
                    </div>
                    <div class="flex justify-between text-[11px] font-bold text-slate-500 uppercase tracking-tight">
                        <span>Pembelian:</span>
                        <span class="text-[#2563eb] font-black">{{ $buyCount }} Transaksi</span>
                    </div>
                </div>
                <p class="text-xs font-black text-[#059669] uppercase mt-3 tracking-tight">TOTAL SUKSES: RP {{ number_format($revenue, 0, ',', '.') }}</p>
            </div>

            {{-- Card 4: Escrow --}}
            <div class="bg-white border border-slate-200 rounded-xl p-8 shadow-sm flex flex-col justify-between h-full min-h-[200px]">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <p class="text-[11px] font-black uppercase tracking-widest text-slate-400">TOTAL ESCROW TERTAHAN</p>
                        <div class="bg-[#b91c1c] text-[10px] font-black text-white px-2 py-0.5 rounded shadow-sm">NEW</div>
                    </div>
                    <h2 class="text-[48px] font-black text-[#b91c1c] leading-none tracking-tighter">Rp {{ number_format($totalEscrow, 0, ',', '.') }}</h2>
                    <p class="text-xs text-slate-400 font-bold mt-3">(Gabungan Sewa & Retur)</p>
                </div>
                <div class="mt-6 space-y-2 border-t border-slate-100 pt-4">
                    <div class="flex justify-between text-[11px] font-bold text-slate-500 uppercase tracking-tight">
                        <span>Jaminan Sewa (Escrow):</span>
                        <span class="text-slate-800 font-black">Rp {{ number_format($jaminanSewaEscrow, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-[11px] font-bold text-slate-500 uppercase tracking-tight">
                        <span>Dana Retur (Escrow):</span>
                        <span class="text-slate-800 font-black">Rp {{ number_format($danaReturEscrow, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 2: Secondary Metrics --}}
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
            <div class="bg-white border border-slate-100 rounded-xl p-5 flex items-center gap-4 shadow-sm">
                <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-100 text-slate-500"><i data-lucide="wallet" class="w-5 h-5"></i></div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Jaminan Sewa (Escrow)</p>
                    <p class="text-lg font-black text-slate-800 leading-none">Rp {{ number_format($jaminanSewaEscrow, 0, ',', '.') }}</p>
                </div>
            </div>
            <div class="bg-white border border-slate-100 rounded-xl p-5 flex items-center gap-4 shadow-sm">
                <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-100 text-slate-500"><i data-lucide="hand-coins" class="w-5 h-5"></i></div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Dana Retur (Escrow)</p>
                    <p class="text-lg font-black text-slate-800 leading-none">Rp {{ number_format($danaReturEscrow, 0, ',', '.') }}</p>
                </div>
            </div>
            <div class="bg-white border border-slate-100 rounded-xl p-5 flex items-center gap-4 shadow-sm">
                <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-100 text-slate-500"><i data-lucide="timer" class="w-5 h-5"></i></div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Avg. Durasi Resolusi</p>
                    <p class="text-lg font-black text-slate-800 leading-none">{{ $avgResolutionTime }}d</p>
                </div>
            </div>
            <div class="bg-white border border-slate-100 rounded-xl p-5 flex items-center gap-4 shadow-sm">
                <div class="p-2.5 bg-emerald-50 rounded-lg border border-emerald-100 text-emerald-600"><i data-lucide="hand-coins" class="w-5 h-5"></i></div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Pendapatan Admin (Fee)</p>
                    <p class="text-lg font-black text-slate-800 leading-none">Rp {{ number_format($adminRentalRevenue, 0, ',', '.') }}</p>
                </div>
            </div>
            <div class="bg-white border border-slate-100 rounded-xl p-5 flex items-center gap-4 shadow-sm">
                <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-100 text-slate-500"><i data-lucide="trending-up" class="w-5 h-5"></i></div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Denda Keterlambatan</p>
                    <p class="text-lg font-black text-slate-800 leading-none">Rp {{ number_format($totalLateFees, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        {{-- Row 3: Alerts --}}
        <div class="grid gap-4 md:grid-cols-2">
            <div class="space-y-4">
                <div class="bg-[#d97706] text-white p-6 rounded-xl flex items-center gap-6 shadow-md border-l-[12px] border-[#92400e]">
                    <div class="p-3 bg-white/20 rounded-xl"><i data-lucide="circle-alert" class="w-8 h-8"></i></div>
                    <div>
                        <p class="text-lg font-black uppercase tracking-tight leading-none">[Peringatan] Ada {{ $overdueReturns }} UNIT SEWA MELEBIHI BATAS (OVERDUE).</p>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-[#064e3b] text-white p-6 rounded-xl flex items-center gap-4">
                    <i data-lucide="calendar-check" class="w-6 h-6 opacity-60"></i>
                    <p class="text-[12px] font-black uppercase leading-tight">[Peringatan] Ada {{ $todayDueRentals }} unit sewa jatuh tempo hari ini.</p>
                </div>
                <div class="bg-[#059669] text-white p-6 rounded-xl flex items-center gap-4">
                    <i data-lucide="info" class="w-6 h-6 opacity-60"></i>
                    <p class="text-[12px] font-black uppercase leading-tight">[Laporan] Ada {{ $todayDueRentals }} unit sewa jatuh tempo hari ini.</p>
                </div>
                <div class="bg-[#d97706] text-white p-6 rounded-xl flex items-center gap-4 col-span-2">
                    <i data-lucide="more-horizontal" class="w-6 h-6 opacity-80 border-2 border-white/50 rounded-full p-0.5"></i>
                    <p class="text-[12px] font-black uppercase leading-tight">[Pengecekan] Ada {{ $pendingReturns }} pengajuan retur (pending) baru.</p>
                </div>
            </div>
        </div>

        {{-- Row 4: Status Cards --}}
        <div class="grid gap-4 grid-cols-2 xl:grid-cols-4">
            <div class="bg-[#fef2f2] border border-[#fee2e2] p-5 rounded-xl flex items-center gap-5">
                <i data-lucide="package" class="w-6 h-6 text-[#ef4444] opacity-50"></i>
                <div>
                    <p class="text-[11px] font-black text-[#991b1b] uppercase tracking-widest leading-none mb-1">PRODUK WAITING</p>
                    <p class="text-xs text-[#991b1b] font-bold opacity-60">{{ $pendingProducts }} produk menunggu validasi</p>
                </div>
            </div>
            <div class="bg-[#f8fafc] border border-[#f1f5f9] p-5 rounded-xl flex items-center gap-5">
                <i data-lucide="ban" class="w-6 h-6 text-slate-400 opacity-50"></i>
                <div>
                    <p class="text-[11px] font-black text-slate-800 uppercase tracking-widest leading-none mb-1">TOKO BANNED</p>
                    <p class="text-xs text-slate-500 font-bold opacity-60">{{ $bannedStores }} toko sedang diblokir</p>
                </div>
            </div>
            <div class="bg-[#eff6ff] border border-[#dbeafe] p-5 rounded-xl flex items-center gap-5">
                <i data-lucide="message-square" class="w-6 h-6 text-[#2563eb] opacity-50"></i>
                <div>
                    <p class="text-[11px] font-black text-[#1e40af] uppercase tracking-widest leading-none mb-1">CHAT BERMASALAH</p>
                    <p class="text-xs text-[#1e40af] font-bold opacity-60">{{ $flaggedChats }} chat ditandai sistem</p>
                </div>
            </div>
            <div class="bg-[#fffbeb] border border-[#fef3c7] p-5 rounded-xl flex items-center gap-5">
                <i data-lucide="user-check" class="w-6 h-6 text-[#d97706] opacity-50"></i>
                <div>
                    <p class="text-[11px] font-black text-[#92400e] uppercase tracking-widest leading-none mb-1">PENDING KYC</p>
                    <p class="text-xs text-[#92400e] font-bold opacity-60">{{ $pendingKyc }} user menunggu verifikasi</p>
                </div>
            </div>
        </div>

        {{-- Row 5: Main Content --}}
        <div class="grid gap-6 xl:grid-cols-[1fr_360px]">
            {{-- Table --}}
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden flex flex-col">
                <div class="flex items-center gap-8 px-8 py-5 border-b border-slate-50">
                    <a href="?filter=all" class="{{ $filter === 'all' || !$filter ? 'bg-[#064e3b] text-white px-6 py-2 rounded-lg' : 'text-slate-400 hover:text-slate-600' }} text-sm font-black uppercase tracking-tight transition-colors">Semua Masalah</a>
                    <a href="?filter=overdue" class="{{ $filter === 'overdue' ? 'bg-[#064e3b] text-white px-6 py-2 rounded-lg' : 'text-slate-400 hover:text-slate-600' }} text-sm font-black uppercase tracking-tight transition-colors">Sewa Terlambat (Overdue)</a>
                    <a href="{{ route('admin.returns.sewa') }}" class="ml-auto text-sm font-black text-[#059669] hover:underline">Lihat Semua</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50/50 border-b border-slate-100">
                            <tr>
                                <th class="px-8 py-4 text-[11px] font-black uppercase text-slate-400 tracking-widest">ID SEWA</th>
                                <th class="px-8 py-4 text-[11px] font-black uppercase text-slate-400 tracking-widest">PRODUK</th>
                                <th class="px-8 py-4 text-[11px] font-black uppercase text-slate-400 tracking-widest">PENYEWA/PEMBELI</th>
                                <th class="px-8 py-4 text-[11px] font-black uppercase text-slate-400 tracking-widest">TGL KEMBALI/DEADLINE</th>
                                <th class="px-8 py-4 text-[11px] font-black uppercase text-slate-400 tracking-widest text-center">DURASI LATE</th>
                                <th class="px-8 py-4 text-[11px] font-black uppercase text-slate-400 tracking-widest text-right">TOTAL ESCROW</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($allIssues as $issue)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-8 py-6 font-black text-[#059669]">#RT-{{ $issue->id }}</td>
                                <td class="px-8 py-6 font-bold text-slate-700 text-xs truncate max-w-[200px]">{{ $issue->order->details->first()->product->name ?? 'Produk' }}</td>
                                <td class="px-8 py-6 font-black text-slate-600 text-xs">{{ $issue->order->buyer->name ?? 'User' }}</td>
                                <td class="px-8 py-6">
                                    @if($issue->expected_date)
                                        <div class="text-[12px] font-black text-[#dc2626]">{{ $issue->expected_date->format('d M Y, H:i') }}</div>
                                        @if($issue->expected_date->isPast())
                                        <div class="text-[9px] font-black uppercase text-[#991b1b] flex items-center gap-1 mt-1">
                                            <div class="w-1.5 h-1.5 bg-[#dc2626] rounded-full"></div> TERLAMBAT {{ $issue->expected_date->diffInHours(now()) }} JAM
                                        </div>
                                        @endif
                                    @else
                                        <div class="text-[12px] font-black text-slate-500">-</div>
                                    @endif
                                </td>
                                <td class="px-8 py-6 text-center text-xs font-black text-slate-600">
                                    {{ $issue->expected_date ? $issue->expected_date->diffInDays(now()) . ' Hari' : '-' }}
                                </td>
                                <td class="px-8 py-6 text-right font-black text-slate-800 text-sm">
                                    <span class="text-[#b91c1c] mr-1">Rp</span>{{ number_format($issue->deposit_amount + $issue->rental_fee_amount, 0, ',', '.') }}
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
                <div class="flex-1 space-y-10 relative before:absolute before:left-[19px] before:top-2 before:bottom-2 before:w-px before:bg-slate-100">
                    @foreach($activityFeed as $act)
                    <div class="relative pl-14">
                        <div class="absolute left-0 top-0 w-10 h-10 rounded-full border-4 border-white shadow flex items-center justify-center
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
                            <p class="text-[11px] font-bold text-slate-400 mt-2 italic">{{ $act['time']->diffForHumans() }}</p>
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
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-1">Total nilai transaksi (Rp) per bulan</p>
                </div>
                <div id="revenueChart" class="h-[280px]"></div>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-8">
                <div class="mb-6">
                    <h3 class="text-lg font-black text-slate-800 tracking-tight">Aktivitas Platform Bulanan</h3>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-1">Perbandingan jumlah transaksi vs pendaftar baru</p>
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
            colors: ['#2563eb', '#cbd5e1'],
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
</script>
@endsection
