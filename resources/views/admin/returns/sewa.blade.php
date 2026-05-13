@extends('layouts.admin')

@section('title', 'Manajemen Pengembalian Sewa')

@inject('settlement', 'App\Services\ReturnSettlementService')

@section('content')
@php
    $badgeMap = [
        'pending' => 'bg-blue-100 text-blue-600',
        'dispute' => 'bg-red-100 text-red-600',
        'checking' => 'bg-indigo-100 text-indigo-600',
        'completed' => 'bg-green-100 text-green-600',
        'rejected' => 'bg-gray-200 text-gray-600',
    ];
@endphp

<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <div class="text-xs text-gray-500 mb-1 flex items-center gap-2">
                <span>Penyewaan</span> <i data-lucide="chevron-right" style="width: 12px; height: 12px;"></i> <span class="font-semibold text-gray-700">Pengembalian Sewa</span>
            </div>
            <h1 class="admin-section-title text-2xl font-bold">Manajemen Pengembalian</h1>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Diharapkan Hari Ini -->
        <div class="admin-card p-5 border border-gray-100 shadow-sm flex flex-col justify-between rounded-xl">
            <div class="flex justify-between items-start mb-4">
                <div class="w-8 h-8 rounded bg-[#f8fbf9] border border-green-100 flex items-center justify-center text-[#0f6b52]">
                    <i data-lucide="calendar" style="width: 16px; height: 16px;"></i>
                </div>
                <span class="text-xs font-semibold text-green-600">+12% vs kemarin</span>
            </div>
            <div>
                <div class="text-[10px] font-bold text-gray-400 tracking-wider uppercase mb-1">Diharapkan Hari Ini</div>
                <div class="flex items-baseline gap-1 mb-2">
                    <span class="text-3xl font-extrabold text-gray-800">{{ $diharapkanHariIni }}</span>
                    <span class="text-sm font-semibold text-gray-500">Unit</span>
                </div>
                <p class="text-xs text-gray-500">Penyewa telah menerima notifikasi SLA.</p>
            </div>
        </div>

        <!-- Overdue -->
        <div class="admin-card p-5 border border-red-100 shadow-sm flex flex-col justify-between rounded-xl relative overflow-hidden">
            <!-- Decorative red line at bottom -->
            <div class="absolute bottom-0 left-0 h-1 bg-red-500 w-1/3"></div>
            <div class="absolute bottom-0 left-1/3 h-1 bg-gray-100 w-2/3"></div>

            <div class="flex justify-between items-start mb-4">
                <div class="w-8 h-8 rounded bg-red-50 border border-red-100 flex items-center justify-center text-red-500">
                    <i data-lucide="alert-triangle" style="width: 16px; height: 16px;"></i>
                </div>
                <span class="text-xs font-semibold text-red-500">Kritis</span>
            </div>
            <div>
                <div class="text-[10px] font-bold text-gray-400 tracking-wider uppercase mb-1">Melebihi Batas (Overdue)</div>
                <div class="flex items-baseline gap-1">
                    <span class="text-3xl font-extrabold text-red-600">{{ str_pad($overdue, 2, '0', STR_PAD_LEFT) }}</span>
                    <span class="text-sm font-semibold text-gray-500">Unit</span>
                </div>
            </div>
        </div>

        <!-- Dalam Pemeriksaan -->
        <div class="admin-card p-5 border border-indigo-100 shadow-sm flex flex-col justify-between rounded-xl">
            <div class="flex justify-between items-start mb-4">
                <div class="w-8 h-8 rounded bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-500">
                    <i data-lucide="clipboard-check" style="width: 16px; height: 16px;"></i>
                </div>
                <span class="text-xs font-semibold text-indigo-500">Antrean</span>
            </div>
            <div>
                <div class="text-[10px] font-bold text-gray-400 tracking-wider uppercase mb-1">Dalam Pemeriksaan</div>
                <div class="flex items-baseline gap-1 mb-2">
                    <span class="text-3xl font-extrabold text-gray-800">{{ str_pad($dalamPemeriksaan, 2, '0', STR_PAD_LEFT) }}</span>
                    <span class="text-sm font-semibold text-gray-500">Unit</span>
                </div>
                <a href="?status=checking" class="text-xs font-semibold text-indigo-600 hover:underline flex items-center gap-1">Lihat Antrean <i data-lucide="arrow-right" style="width: 12px; height: 12px;"></i></a>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="flex flex-wrap items-center justify-between gap-4 mt-8">
        <form method="GET" action="{{ route('admin.returns.sewa') }}" class="flex flex-wrap gap-3 items-center">
            <div class="relative">
                <i data-lucide="filter" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" style="width: 14px; height: 14px;"></i>
                <select class="admin-form-control text-sm py-2 pl-9 rounded-md bg-white border border-gray-200 w-[150px]" name="status" onchange="this.form.submit()">
                    <option value="">Filter Status</option>
                    @foreach(['pending', 'dispute', 'checking', 'completed', 'rejected'] as $st)
                        <option value="{{ $st }}" @selected(request('status') === $st)>{{ ucfirst($st) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="relative">
                <i data-lucide="calendar" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" style="width: 14px; height: 14px;"></i>
                <button type="button" class="admin-form-control text-sm py-2 pl-9 rounded-md bg-white border border-gray-200 text-gray-500 hover:bg-gray-50 text-left w-[130px]">
                    Periode
                </button>
            </div>
            @if(request('status'))
                <a href="{{ route('admin.returns.sewa') }}" class="text-xs text-gray-400 hover:text-gray-700 ml-2">Reset</a>
            @endif
        </form>
        <a href="{{ route('admin.returns.export.sewa') }}" class="px-4 py-2 text-sm font-semibold bg-white border border-gray-200 rounded-md text-[#0f6b52] hover:bg-gray-50 flex items-center gap-2">
            <i data-lucide="download" style="width: 14px; height: 14px;"></i> Export CSV
        </a>
    </div>

    <!-- Table -->
    <div class="admin-card border border-gray-100 mt-4 rounded-xl overflow-hidden">
        <div class="admin-table-wrap overflow-x-auto">
            <table class="admin-table w-full text-sm">
                <thead class="bg-[#f8fbf9] border-b border-gray-100 text-gray-500 text-xs tracking-wider">
                    <tr>
                        <th class="py-4 px-4 text-left font-bold">ID SEWA</th>
                        <th class="py-4 px-4 text-left font-bold">PRODUK</th>
                        <th class="py-4 px-4 text-left font-bold">PENYEWA</th>
                        <th class="py-4 px-4 text-left font-bold">TGL KEMBALI (SLA)</th>
                        <th class="py-4 px-4 text-center font-bold">DURASI</th>
                        <th class="py-4 px-4 text-center font-bold">DENDA</th>
                        <th class="py-4 px-4 text-center font-bold">STATUS</th>
                        <th class="py-4 px-4 text-center font-bold">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($returns as $item)
                        @php
                            $product = $item->order->details->first()->product;
                            $productName = $product->name ?? 'Produk';
                            $productCategory = $product->category->name ?? 'Kategori';
                            $buyerName = $item->order->user->name ?? 'Penyewa';
                            $isOverdue = $item->expected_date && $item->expected_date < now() && !in_array($item->status, ['completed', 'rejected']);
                            $lateString = $settlement->formatLateDuration($item->expected_date, $item->actual_date);
                            
                            // Hitung denda keterlambatan live dari DB (atau hitung dari overdue jika belum tersimpan)
                            $liveLateFeeDays = 0;
                            if ($isOverdue) {
                                $expected = \Carbon\Carbon::parse($item->expected_date)->startOfDay();
                                $actual   = $item->actual_date ? \Carbon\Carbon::parse($item->actual_date)->startOfDay() : now()->startOfDay();
                                $liveLateFeeDays = max(0, (int) $expected->diffInDays($actual, false));
                            }
                            $dailyRentTotal = $item->rental_fee_amount > 0 ? $item->rental_fee_amount : ($item->order->details->first()->harga ?? 0);
                            $liveFinePerDay = (int) ($dailyRentTotal * 0.3);
                            $liveTotalLateFee = ($item->late_fee > 0) ? (int)$item->late_fee : ($liveLateFeeDays * $liveFinePerDay);
                            
                            $statusMap = [
                                'dispute' => ['text' => 'SENGKETA', 'class' => 'bg-red-100 text-red-600'],
                                'pending' => ['text' => $isOverdue ? 'TERLAMBAT' : 'AKTIF', 'class' => $isOverdue ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600'],
                                'checking' => ['text' => 'PEMERIKSAAN', 'class' => 'bg-indigo-100 text-indigo-600'],
                                'completed' => ['text' => 'SELESAI', 'class' => 'bg-gray-100 text-gray-600'],
                                'rejected' => ['text' => 'DITOLAK', 'class' => 'bg-gray-100 text-gray-600'],
                            ];
                            $statusInfo = $statusMap[$item->status] ?? ['text' => strtoupper($item->status), 'class' => 'bg-gray-100 text-gray-600'];
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="py-4 px-4 font-bold text-[#0f6b52]">#RT-{{ 99200 + $item->id }}</td>
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded bg-gray-100 flex items-center justify-center shrink-0">
                                        <i data-lucide="package" style="width: 16px; height: 16px;" class="text-gray-400"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-800">{{ Str::limit($productName, 25) }}</div>
                                        <div class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">{{ $productCategory }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="font-bold text-gray-800">{{ $buyerName }}</div>
                                @if($item->order->user->ktp_verified_at)
                                    <div class="flex items-center gap-1 text-[9px] font-black text-blue-600 uppercase tracking-widest mt-1">
                                        <i data-lucide="shield-check" style="width: 10px; height: 10px;"></i> KTP Terverifikasi
                                    </div>
                                @else
                                    <div class="flex items-center gap-1 text-[9px] font-black text-amber-500 uppercase tracking-widest mt-1">
                                        <i data-lucide="shield-alert" style="width: 10px; height: 10px;"></i> Belum Verifikasi
                                    </div>
                                @endif
                            </td>
                            <td class="py-4 px-4">
                                @if($item->actual_date)
                                    <div class="font-semibold text-blue-600">{{ $item->actual_date->format('d M Y, H:i') }}</div>
                                    <div class="text-[9px] font-bold text-gray-400 uppercase mt-0.5">SLA: {{ $item->expected_date ? $item->expected_date->format('d M Y, H:i') : '-' }}</div>
                                @else
                                    <div class="font-semibold text-gray-700 {{ $isOverdue ? 'text-red-500' : '' }}">{{ $item->expected_date ? $item->expected_date->format('d M Y, H:i') : '-' }}</div>
                                    @if($isOverdue)
                                        <div class="text-[10px] font-bold text-red-500 uppercase mt-0.5">⚠️ TERLAMBAT {{ $lateString }}</div>
                                    @elseif($item->expected_date && $item->expected_date->isToday())
                                        <div class="text-[10px] font-bold text-[#0f6b52] uppercase mt-0.5">⏰ HARI INI</div>
                                    @endif
                                @endif
                            </td>
                            <td class="py-4 px-4 text-center">
                                <div class="font-bold text-gray-800">{{ $item->order->details->first()->duration ?? '-' }} Hari</div>
                            </td>
                            <td class="py-4 px-4">
                                @if($item->deposit_amount > 0)
                                    <div class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-1">Jaminan: Rp {{ number_format((int)$item->deposit_amount, 0, ',', '.') }}</div>
                                @endif
                                @if($liveTotalLateFee > 0)
                                    <div class="text-[10px] text-orange-600 font-bold">⏱ Terlambat: Rp {{ number_format($liveTotalLateFee, 0, ',', '.') }}</div>
                                    @if($liveLateFeeDays > 0)
                                        <div class="text-[9px] text-orange-400">{{ $liveLateFeeDays }} hari × Rp {{ number_format($liveFinePerDay, 0, ',', '.') }}</div>
                                    @endif
                                @endif
                                @if($item->damage_fee > 0)
                                    <div class="text-[10px] text-red-600 font-bold">🔧 Kerusakan: Rp {{ number_format((int)$item->damage_fee, 0, ',', '.') }}</div>
                                @endif
                                @if($liveTotalLateFee <= 0 && $item->damage_fee <= 0)
                                    <div class="font-semibold text-gray-400 text-xs">Tidak Ada Denda</div>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-center">
                                <span class="inline-block px-2.5 py-1 text-[9px] font-bold tracking-wider rounded-full {{ $statusInfo['class'] }}">{{ $statusInfo['text'] }}</span>
                            </td>
                            <td class="py-4 px-4 text-center">
                                @php
                                    $isActualDispute = $item->status === 'dispute' || ($item->status === 'completed' && ($item->damage_fee > 0 || !empty($item->dispute_chat_log)));
                                @endphp
                                @if($isActualDispute && $item->status !== 'completed')
                                    <a href="{{ route('admin.returns.show', $item->id) }}"
                                       class="inline-flex items-center justify-center w-36 h-9 bg-red-600 hover:bg-red-700 text-white text-[9.5px] font-bold rounded-lg shadow-sm transition-all duration-200 hover:-translate-y-0.5 active:scale-95 whitespace-nowrap px-2">
                                        Resolusi Sengketa
                                    </a>
                                @else
                                    <a href="{{ route('admin.returns.show', $item->id) }}"
                                       class="inline-flex items-center justify-center w-36 h-9 bg-[#0f6b52] hover:bg-[#0c5843] text-white text-[9.5px] font-bold rounded-lg shadow-sm transition-all duration-200 hover:-translate-y-0.5 active:scale-95 whitespace-nowrap px-2">
                                        Proses
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-500">Tidak ada data penyewaan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100 bg-[#f8fbf9] flex items-center justify-between">
            <div class="text-xs text-gray-500">
                Menampilkan {{ $returns->firstItem() ?? 0 }}-{{ $returns->lastItem() ?? 0 }} dari {{ $returns->total() }} transaksi
            </div>
            <div class="flex gap-1">
                {{ $returns->links() }}
            </div>
        </div>
    </div>

    <!-- Bottom Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-6">
        <div class="lg:col-span-2 admin-card bg-[#0f6b52] border-none p-6 text-white flex gap-6 relative overflow-hidden rounded-xl">
            <!-- decorative icon -->
            <i data-lucide="clipboard-check" class="absolute -right-4 -bottom-4 text-white/10" style="width: 140px; height: 140px;"></i>
            
            <div class="relative z-10 w-full">
                <h3 class="font-bold text-lg mb-2">Protokol Kerusakan Unit</h3>
                <p class="text-sm text-white/80 leading-relaxed mb-6 max-w-lg">Pastikan untuk mengambil foto dokumentasi setiap kali ditemukan kerusakan baru pada unit saat pengembalian untuk mempermudah proses resolusi klaim asuransi.</p>
                <button class="bg-white text-[#0f6b52] hover:bg-gray-50 text-sm font-bold py-2 px-5 rounded">
                    Baca Panduan Pemeriksaan
                </button>
            </div>
        </div>

        <div class="admin-card p-6 border border-gray-100 rounded-xl">
            <h3 class="font-bold text-gray-800 mb-6 text-xs tracking-wider uppercase">SLA Penanganan</h3>
            
            <div class="mb-5">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-xs font-bold text-gray-600">Pemeriksaan Unit</span>
                    <span class="text-xs text-gray-500">Target: <span class="font-semibold text-blue-600">2 Jam</span></span>
                </div>
                <div class="h-1.5 w-full bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-[#0f6b52] rounded-full" style="width: 70%"></div>
                </div>
            </div>
            
            <div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-xs font-bold text-gray-600">Pencairan Deposit</span>
                    <span class="text-xs text-gray-500">Target: <span class="font-semibold text-blue-600">24 Jam</span></span>
                </div>
                <div class="h-1.5 w-full bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-blue-600 rounded-full" style="width: 35%"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
