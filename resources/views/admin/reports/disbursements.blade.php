@extends('admin.reports.layout')

@section('title', 'Laporan Pencairan Dana - CAMPIFY')
@section('report_title', 'Laporan Pencairan Dana per Seller Order')

@section('content')
    <table class="stat-boxes" cellpadding="0" cellspacing="8">
        <tr>
            <td class="stat-box" width="33%">
                <div class="stat-value">{{ $sellerOrders->count() }}</div>
                <div class="stat-label">Seller Order</div>
            </td>
            <td class="stat-box" width="33%">
                <div class="stat-value" style="color: #059669;">Rp {{ number_format($sellerOrders->sum('subtotal'), 0, ',', '.') }}</div>
                <div class="stat-label">Total Nominal</div>
            </td>
            <td class="stat-box" width="33%">
                <div class="stat-value" style="color: #1e40af;">{{ $sellerOrders->where('payout.status', 'READY_TO_DISBURSE')->count() }}</div>
                <div class="stat-label">Ready</div>
            </td>
        </tr>
    </table>

    <div class="section-title">Daftar Pencairan</div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="13%">Seller Order</th>
                <th width="13%">Order</th>
                <th width="17%">Seller</th>
                <th width="15%">Pembeli</th>
                <th width="16%">Produk</th>
                <th width="12%" class="text-right">Nominal</th>
                <th width="14%" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sellerOrders as $sellerOrder)
                @php
                    $order = $sellerOrder->order;
                    $store = $sellerOrder->store;
                    $sellerName = $store?->nama_toko ?? $sellerOrder->seller?->name ?? 'Seller tidak ditemukan';
                    $productNames = $sellerOrder->items
                        ->map(fn($item) => $item->product?->name ?? 'Produk')
                        ->join(', ');
                    $status = $sellerOrder->payout?->status ?? 'WAITING_DELIVERY';
                @endphp
                <tr>
                    <td class="text-bold">{{ $sellerOrder->seller_order_number ?? 'SO-' . $sellerOrder->id }}</td>
                    <td>{{ $order?->order_number ?? '#ORD-' . $sellerOrder->order_id }}</td>
                    <td>{{ $sellerName }}</td>
                    <td>{{ $order?->buyer?->name ?? '-' }}</td>
                    <td>{{ $productNames }}</td>
                    <td class="text-right text-bold">Rp {{ number_format($sellerOrder->subtotal, 0, ',', '.') }}</td>
                    <td class="text-center">
                        @if($status === 'DISBURSED')
                            <span class="badge badge-green">DISBURSED</span>
                        @elseif($status === 'READY_TO_DISBURSE')
                            <span class="badge badge-blue">READY</span>
                        @elseif($status === 'WAITING_HOLD')
                            <span class="badge badge-yellow">WAITING HOLD</span>
                        @else
                            <span class="badge badge-gray">WAITING DELIVERY</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 15px; color: #475569;">Tidak ada data pencairan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
