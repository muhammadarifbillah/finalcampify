@extends('admin.reports.layout')

@section('title', 'Pengembalian Pembelian - CAMPIFY')
@section('report_title', 'Pengembalian Pembelian')

@section('content')
    @php
        $totalValue = $returns->sum('escrow_total');
        $totalRefund = $returns->sum('to_buyer');
        $totalDisbursed = $returns->sum('to_seller');
        $totalFines = $returns->sum('late_fee') + $returns->sum('damage_fee');
    @endphp

    <table class="stat-boxes" cellpadding="0" cellspacing="8">
        <tr>
            <td class="stat-box" width="25%">
                <div class="stat-value">{{ count($returns) }}</div>
                <div class="stat-label">Total Retur</div>
            </td>
            <td class="stat-box" width="25%">
                <div class="stat-value" style="color: #2563eb;">Rp {{ number_format($totalValue, 0, ',', '.') }}</div>
                <div class="stat-label">Total Nilai Retur</div>
            </td>
            <td class="stat-box" width="25%">
                <div class="stat-value" style="color: #059669;">Rp {{ number_format($totalRefund, 0, ',', '.') }}</div>
                <div class="stat-label">Total Refund ke Pembeli</div>
            </td>
            <td class="stat-box" width="25%">
                <div class="stat-value" style="color: #059669;">Rp {{ number_format($totalDisbursed, 0, ',', '.') }}</div>
                <div class="stat-label">Total Disalurkan ke Toko</div>
            </td>
        </tr>
    </table>

    <div class="section-title">Daftar Retur Pembelian</div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="10%">ID Retur</th>
                <th width="15%">Pembeli</th>
                <th width="18%">Produk</th>
                <th width="12%" class="text-right">Total Retur (Rp)</th>
                <th width="12%" class="text-right">Refund ke Pembeli (Rp)</th>
                <th width="12%" class="text-right">Penyaluran ke Toko (Rp)</th>
                <th width="13%" class="text-center">Tanggal Retur</th>
                <th width="10%">Status</th>
                <th width="10%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($returns as $r)
                @php
                    $statusLabel = match (strtolower($r->status)) {
                        'completed', 'selesai' => 'Selesai',
                        'checking' => 'Checking',
                        'pending' => 'Pending',
                        'rejected' => 'Ditolak',
                        default => ucfirst($r->status),
                    };
                    $statusBadge = match (strtolower($r->status)) {
                        'completed', 'selesai' => 'badge badge-green',
                        'checking' => 'badge badge-yellow',
                        'pending' => 'badge badge-gray',
                        'rejected' => 'badge badge-red',
                        default => 'badge badge-gray',
                    };
                    $note = 'Menunggu proses retur.';
                    if (in_array(strtolower($r->status), ['completed', 'selesai'])) {
                        $note = 'Retur selesai dan dana refund diproses.';
                    } elseif (strtolower($r->status) === 'rejected') {
                        $note = 'Retur ditolak oleh seller/admin.';
                    }
                    $product = $r->order && $r->order->details ? $r->order->details->first()->product : null;
                    $productName = $product ? $product->name : 'Produk N/A';
                    $storeName = $product && $product->store ? $product->store->nama_toko : 'Toko N/A';
                @endphp
                <tr>
                    <td class="text-bold">#RT-{{ $r->id }}<br><span style="font-size: 6.5pt; color: #475569;">Order
                            #{{ $r->order_id }}</span></td>
                    <td>
                        {{ $r->order->buyer->name ?? 'N/A' }}<br>
                        <span style="font-size: 6.5pt; color: #475569;">{{ $r->order->buyer->phone ?? '' }}</span>
                    </td>
                    <td>
                        {{ $productName }}<br>
                        <span style="font-size: 6.5pt; color: #475569;">Toko: {{ $storeName }}</span>
                    </td>
                    <td class="text-right">Rp {{ number_format($r->escrow_total, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($r->to_buyer, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($r->to_seller, 0, ',', '.') }}</td>
                    <td class="text-center">
                        <span
                            style="font-size: 7.5pt;">{{ $r->actual_date ? $r->actual_date->format('d/m/Y') : ($r->created_at ? $r->created_at->format('d/m/Y') : '-') }}</span>
                    </td>
                    <td><span class="{{ $statusBadge }}">{{ $statusLabel }}</span></td>
                    <td>{{ $note }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 15px; color: #475569;">Tidak ada data retur pembelian
                        pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if(count($returns) > 0)
        <div class="page-break"></div>
        <div class="section-title" style="margin-top: 0;">Catatan & Distribusi Keuangan</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th width="18%">ID Retur</th>
                    <th width="24%">Catatan Seller</th>
                    <th width="24%">Catatan Pembeli</th>
                    <th width="18%" class="text-right">Refund ke Pembeli</th>
                    <th width="18%" class="text-right">Penyaluran ke Toko</th>
                </tr>
            </thead>
            <tbody>
                @foreach($returns as $r)
                    <tr>
                        <td class="text-bold">#RT-{{ $r->id }}</td>
                        <td style="font-size: 7.5pt; color: #475569;">{{ $r->owner_notes ?? '-' }}</td>
                        <td style="font-size: 7.5pt; color: #475569;">{{ $r->renter_notes ?? '-' }}</td>
                        <td class="text-right text-bold" style="color: #2563eb;">Rp {{ number_format($r->to_buyer, 0, ',', '.') }}
                        </td>
                        <td class="text-right text-bold" style="color: #059669;">Rp {{ number_format($r->to_seller, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection