@extends('admin.reports.layout')

@section('title', 'Laporan Pencairan Dana - CAMPIFY')
@section('report_title', 'Laporan Pencairan Dana')

@section('content')
    {{-- Summary Cards --}}
    <table class="stat-boxes" cellpadding="0" cellspacing="8">
        <tr>
            <td class="stat-box" width="33%">
                <div class="stat-value">{{ $payouts->count() }}</div>
                <div class="stat-label">Total Transaksi</div>
            </td>
            <td class="stat-box" width="33%">
                <div class="stat-value" style="color: #059669;">Rp {{ number_format($payouts->sum('amount'), 0, ',', '.') }}</div>
                <div class="stat-label">Total Dana Dicairkan</div>
            </td>
            <td class="stat-box" width="33%">
                <div class="stat-value" style="color: #1e40af;">{{ $payouts->where('status', 'DISBURSED')->count() }}</div>
                <div class="stat-label">Selesai (Disbursed)</div>
            </td>
        </tr>
    </table>

    {{-- Main Data Table --}}
    <div class="section-title">Rincian Pencairan Dana</div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="8%">ID</th>
                <th width="15%">No. SO / Order</th>
                <th width="20%">Toko / Seller</th>
                <th width="15%" class="text-right">Jumlah (Rp)</th>
                <th width="12%" class="text-center">Status</th>
                <th width="12%" class="text-center">Waktu Diterima</th>
                <th width="10%">Sumber</th>
                <th width="8%">Waktu Cair</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payouts as $p)
                @php
                    $order = $p->sellerOrder?->order;
                    $receivedAt = $order?->received_at ?? $p->sellerOrder?->delivered_at ?? null;
                    $sourceLabel = 'N/A';
                    if ($p->source) {
                        $sourceLabel = $p->source === 'auto' ? 'Otomatis' : ($p->source === 'manual' ? 'Manual' : ucfirst($p->source));
                    } elseif ($p->disbursed_at) {
                        $sourceLabel = 'Manual';
                    }
                    
                    $status = strtolower($p->status);
                    $badgeClass = 'badge-gray';
                    if ($status === 'disbursed' || $status === 'selesai') {
                        $badgeClass = 'badge-green';
                    } elseif ($status === 'ready' || $status === 'ready_to_disburse') {
                        $badgeClass = 'badge-blue';
                    } elseif ($status === 'pending' || $status === 'waiting_hold') {
                        $badgeClass = 'badge-yellow';
                    } elseif ($status === 'failed') {
                        $badgeClass = 'badge-red';
                    }
                @endphp
                <tr>
                    <td class="text-bold">#PY-{{ $p->id }}</td>
                    <td>
                        <span class="text-bold" style="font-size: 7.5pt;">{{ $p->sellerOrder?->seller_order_number ?? '-' }}</span><br>
                        <span style="font-size: 6.5pt; color: #475569;">{{ $p->sellerOrder?->order?->order_number ?? '-' }}</span>
                    </td>
                    <td>
                        {{ $p->sellerOrder?->store?->nama_toko ?? $p->sellerOrder?->seller?->name ?? '-' }}
                    </td>
                    <td class="text-right text-bold">
                        Rp {{ number_format($p->amount, 0, ',', '.') }}
                    </td>
                    <td class="text-center">
                        <span class="badge {{ $badgeClass }}">{{ str_replace('_', ' ', strtoupper($p->status)) }}</span>
                    </td>
                    <td class="text-center" style="font-size: 7.5pt;">
                        {{ $receivedAt?->format('d/m/Y') ?? '-' }}
                    </td>
                    <td>
                        {{ $sourceLabel }}
                    </td>
                    <td style="font-size: 7.5pt;">
                        {{ $p->disbursed_at?->format('d/m/Y H:i') ?? '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 15px; color: #475569;">
                        Tidak ada data pencairan dana.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection