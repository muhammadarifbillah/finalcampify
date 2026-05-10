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
                    <p class="text-xs text-slate-500 font-bold mt-3">{{ $sellers }} penjual aktif terdaftar</p>
                </div>
                <div class="flex gap-1.5 h-14 items-end mt-4">
                    <div class="w-1/5 bg-slate-100 rounded-sm h-[30%]"></div>
                    <div class="w-1/5 bg-slate-200 rounded-sm h-[50%]"></div>
                    <div class="w-1/5 bg-slate-100 rounded-sm h-[40%]"></div>
                    <div class="w-1/5 bg-[#059669]/40 rounded-sm h-[75%]"></div>
                    <div class="w-1/5 bg-[#064e3b] rounded-sm h-[100%]"></div>
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
                    <h2 class="text-[48px] font-black text-slate-800 leading-none tracking-tighter">Rp {{ number_format($revenue / 1000000, 1) }}M</h2>
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
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
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
                <div class="bg-[#b91c1c] text-white p-6 rounded-xl flex items-center gap-6 shadow-md border-l-[12px] border-[#7f1d1d]">
                    <div class="p-3 bg-white/20 rounded-xl"><i data-lucide="alert-triangle" class="w-8 h-8"></i></div>
                    <div>
                        <p class="text-lg font-black uppercase tracking-tight leading-none">[Urgensi] Ada {{ $disputeReturns }} SENGKETA AKTIF (Jual-Beli & Sewa)</p>
                        <p class="text-xs font-bold opacity-80 mt-2">yang butuh penanganan Admin.</p>
                    </div>
                </div>
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
                    <p class="text-[12px] font-black uppercase leading-tight">[Pengecekan] Ada {{ $pendingReturnsCount }} pengajuan retur (pending) baru.</p>
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
                    <a href="?filter=dispute" class="{{ $filter === 'dispute' ? 'bg-[#064e3b] text-white px-6 py-2 rounded-lg' : 'text-slate-400 hover:text-slate-600' }} text-sm font-black uppercase tracking-tight transition-colors">Retur Sengketa (Urgent)</a>
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
                                    <div class="text-[12px] font-black text-[#dc2626]">{{ $issue->expected_date->format('d M Y, H:i') }}</div>
                                    <div class="text-[9px] font-black uppercase text-[#991b1b] flex items-center gap-1 mt-1">
                                        <div class="w-1.5 h-1.5 bg-[#dc2626] rounded-full"></div> TERLAMBAT {{ $issue->expected_date->diffInHours(now()) }} MENIT
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-center text-xs font-black text-slate-600">{{ $issue->expected_date->diffInDays(now()) }} Hari</td>
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

        {{-- Row 6: Bottom Widgets --}}
        <div class="grid gap-6 md:grid-cols-2">
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-8">
                <p class="text-[11px] font-black uppercase tracking-[0.2em] text-slate-400 mb-10">SENGKETA PALING BERLARUT (TOP 5)</p>
                <div class="h-64 flex items-end justify-between px-6 gap-6 pb-6">
                    @foreach(['#RT-99214', '#RT-99202', '#RT-99110', '#RT-88204', '#RT-99207'] as $id)
                    <div class="flex-1 flex flex-col items-center justify-end gap-4 h-full">
                        <div class="w-full bg-teal-100 border-t-4 border-teal-600 rounded-t-md" style="height: {{ rand(40, 95) }}%"></div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">{{ $id }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-8">
                <div class="flex items-center justify-between mb-10">
                    <p class="text-[11px] font-black uppercase tracking-[0.2em] text-slate-400">RATA-RATA WAKTU RESOLUSI</p>
                    <span class="text-xl font-black text-[#064e3b]">{{ abs($avgResolutionTime) }}d</span>
                </div>
                <div class="h-64 relative pt-10 pb-6">
                    <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                        <path d="M0,80 L20,65 L40,70 L60,35 L80,90 L100,60" fill="none" stroke="#0f766e" stroke-width="4" vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                    <div class="absolute bottom-0 left-0 right-0 flex justify-between px-2 text-[10px] font-black text-slate-300 uppercase tracking-widest">
                        <span>Jan</span><span>Mar</span><span>May</span><span>Jul</span><span>Sep</span><span>Nov</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
