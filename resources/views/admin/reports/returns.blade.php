@extends('admin.reports.layout')

@section('title', ($returnTypeLabel ?? 'Laporan Retur & Escrow') . ' - CAMPIFY')
@section('report_title', $returnTypeLabel ?? 'Laporan Retur & Escrow')

@section('content')
    @php
        $isRentalReturn = ($returnType ?? 'sewa') === 'sewa';
        $mainHeader = $returnTypeLabel ?? 'Laporan Retur & Escrow';
        $tableTitle = $isRentalReturn ? 'Daftar Retur & Escrow Jaminan' : 'Daftar Retur Pembelian';
        $dateLabel = $isRentalReturn ? 'Jadwal Kembali' : 'Tanggal Retur';
        $customerLabel = $isRentalReturn ? 'Penyewa/Buyer' : 'Pembeli';
        $summaryAmount = $isRentalReturn ? $returns->sum('deposit_amount') : $returns->sum('escrow_total');
        $summaryAmountLabel = $isRentalReturn ? 'Total Jaminan (Deposit)' : 'Total Nilai Retur';
    @endphp

    {{-- Summary Cards --}}
    <table class="stat-boxes" cellpadding="0" cellspacing="8">
        <tr>
            <td class="stat-box" width="25%">
                <div class="stat-value">{{ count($returns) }}</div>
                <div class="stat-label">Total Retur/Escrow</div>
            </td>
            <td class="stat-box" width="25%">
                <div class="stat-value" style="color: #2563eb;">Rp {{ number_format($summaryAmount, 0, ',', '.') }}</div>
                <div class="stat-label">{{ $summaryAmountLabel }}</div>
            </td>
            <td class="stat-box" width="25%">
                <div class="stat-value" style="color: #d97706;">Rp {{ number_format($returns->sum('late_fee') + $returns->sum('damage_fee'), 0, ',', '.') }}</div>
                <div class="stat-label">Total Denda</div>
            </td>
            <td class="stat-box" width="25%">
                <div class="stat-value" style="color: #059669;">Rp {{ number_format($returns->sum('to_seller'), 0, ',', '.') }}</div>
                <div class="stat-label">Disalurkan ke Toko</div>
            </td>
        </tr>
    </table>

    {{-- Main Data Table --}}
    <div class="section-title">{{ $tableTitle }}</div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="10%">ID Retur</th>
                <th width="15%">{{ $customerLabel }}</th>
                <th width="18%">Produk</th>
                <th width="12%" class="text-right">Jaminan (Rp)</th>
                <th width="12%" class="text-right">Denda (Rp)</th>
                <th width="13%" class="text-center">{{ $dateLabel }}</th>
                <th width="10%">Status</th>
                <th width="10%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($returns as $r)
                @php
                    $isLate = $r->expected_date && $r->expected_date->isPast() && !$r->actual_date;
                    $lateDays = $isLate ? round($r->expected_date->diffInDays(now())) : 0;
                @endphp
                <tr>
                    <td class="text-bold">#RT-{{ $r->id }}<br><span style="font-size: 6.5pt; color: #64748b;">Order #{{ $r->order_id }}</span></td>
                    <td>
                        {{ $r->order->buyer->name ?? 'N/A' }}<br>
                        <span style="font-size: 6.5pt; color: #64748b;">{{ $r->order->buyer->phone ?? '' }}</span>
                    </td>
                    <td>
                        @php
                            $firstDetail = $r->order && $r->order->details ? $r->order->details->first() : null;
                            $prodName = $firstDetail && $firstDetail->product ? $firstDetail->product->name : 'Produk N/A';
                            $storeName = $firstDetail && $firstDetail->product && $firstDetail->product->store ? $firstDetail->product->store->nama_toko : 'N/A';
                        @endphp
                        {{ $prodName }}<br>
                        <span style="font-size: 6.5pt; color: #64748b;">Toko: {{ $storeName }}</span>
                    </td>
                    <td class="text-right">
                        Rp {{ number_format($r->deposit_amount, 0, ',', '.') }}
                    </td>
                    <td class="text-right text-red">
                        @if($r->late_fee > 0 || $r->damage_fee > 0)
                            Rp {{ number_format($r->late_fee + $r->damage_fee, 0, ',', '.') }}
                            <div style="font-size: 6pt; color: #64748b;">
                                @if($r->late_fee > 0) Tl: {{ number_format($r->late_fee, 0, ',', '.') }} @endif
                                @if($r->damage_fee > 0) Rs: {{ number_format($r->damage_fee, 0, ',', '.') }} @endif
                            </div>
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center">
                        @if($isRentalReturn)
                            <span style="font-size: 7.5pt;">{{ $r->expected_date ? $r->expected_date->format('d/m/Y') : '-' }}</span>
                        @else
                            <span style="font-size: 7.5pt;">{{ $r->actual_date ? $r->actual_date->format('d/m/Y') : ($r->created_at ? $r->created_at->format('d/m/Y') : '-') }}</span>
                        @endif
                        @if($r->actual_date && $isRentalReturn)
                            <div style="font-size: 6.5pt; color: #059669;">Aktual: {{ $r->actual_date->format('d/m/Y') }}</div>
                        @endif
                    </td>
                    <td>
                        @switch(strtolower($r->status))
                            @case('completed')
                            @case('selesai')
                                <span class="badge badge-green">Selesai</span>
                                @break
                            @case('checking')
                                <span class="badge badge-yellow">Checking</span>
                                @break
                            @case('pending')
                                <span class="badge badge-gray">Pending</span>
                                @break
                            @default
                                <span class="badge badge-gray">{{ $r->status }}</span>
                        @endswitch
                    </td>
                    <td>
                        @if($isLate)
                            <span class="badge badge-red" style="font-size: 6.5pt;">Terlambat {{ $lateDays }} Hari</span>
                        @elseif($r->actual_date && $r->expected_date && $r->actual_date->gt($r->expected_date))
                            <span style="font-size: 7.5pt; color: #b45309;">Kembali Terlambat</span>
                        @else
                            <span style="font-size: 7.5pt; color: #64748b;">Tepat Waktu</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 15px; color: #64748b;">
                        Tidak ada data retur atau escrow pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Detail Catatan & Distribusi Escrow --}}
    @if(count($returns) > 0)
        <div class="page-break"></div>
        <div class="section-title" style="margin-top: 0;">Rincian Catatan & Distribusi Keuangan</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th width="12%">ID Retur</th>
                    <th width="20%" class="text-right">Diterima Pembeli (Refund)</th>
                    <th width="20%" class="text-right">Diterima Toko (Penyaluran)</th>
                    <th width="24%">Catatan Pemilik Toko</th>
                    <th width="24%">Catatan Penyewa</th>
                </tr>
            </thead>
            <tbody>
                @foreach($returns as $r)
                    <tr>
                        <td class="text-bold">#RT-{{ $r->id }}</td>
                        <td class="text-right text-bold" style="color: #2563eb;">
                            Rp {{ number_format($r->to_buyer, 0, ',', '.') }}
                        </td>
                        <td class="text-right text-bold" style="color: #059669;">
                            Rp {{ number_format($r->to_seller, 0, ',', '.') }}
                        </td>
                        <td style="font-size: 7.5pt; color: #475569;">
                            {{ $r->owner_notes ?? '-' }}
                        </td>
                        <td style="font-size: 7.5pt; color: #475569;">
                            {{ $r->renter_notes ?? '-' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
