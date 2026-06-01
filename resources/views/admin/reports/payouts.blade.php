<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pencairan</title>
    <style>
        @if(!empty($customFont) && isset($customFont['path']))
            @font-face {
                font-family: '{{ $customFont['family'] }}';
                src: url("file://{{ $customFont['path'] }}") format('truetype');
                font-weight: normal;
                font-style: normal;
            }
            body { font-family: '{{ $customFont['family'] }}', DejaVu Sans, sans-serif; font-size: 12px; }
        @else
            body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        @endif
        .header { text-align: center; margin-bottom: 10px }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px; }
        th { background: #f3f3f3; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Pencairan</h2>
        <div>Dicetak: {{ $printDate ?? now()->format('d/m/Y H:i:s') }}</div>
        @if(!empty($filters))
            <div style="margin-top:6px;font-size:11px;color:#666">Saring: {{ http_build_query($filters) }}</div>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>SO</th>
                <th>Order</th>
                <th>Seller</th>
                <th class="right">Jumlah</th>
                <th>Status Pencairan</th>
                <th>Diterima</th>
                <th>Sumber</th>
                <th>Waktu Dicairkan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payouts as $p)
                <tr>
                    <td>{{ $p->id }}</td>
                    <td>{{ $p->sellerOrder?->seller_order_number ?? '-' }}</td>
                    <td>{{ $p->sellerOrder?->order?->order_number ?? '-' }}</td>
                    <td>{{ $p->sellerOrder?->store?->nama_toko ?? $p->sellerOrder?->seller?->name ?? '-' }}</td>
                    <td class="right">Rp {{ number_format($p->amount, 0, ',', '.') }}</td>
                    <td>{{ $p->status }}</td>
                    @php
                        $order = $p->sellerOrder?->order;
                        $receivedAt = $order?->received_at ?? $p->sellerOrder?->delivered_at ?? null;
                        $sourceLabel = 'Tidak tersedia';
                        if ($p->source) {
                            $sourceLabel = $p->source === 'auto' ? 'Otomatis (Scheduler)' : ($p->source === 'manual' ? 'Manual (Admin)' : ucfirst($p->source));
                        } elseif ($p->disbursed_at) {
                            $sourceLabel = 'Manual (tidak diketahui)';
                        }
                    @endphp
                    <td>{{ $receivedAt?->format('d/m/Y') ?? '-' }}</td>
                    <td>{{ $sourceLabel }}</td>
                    <td>{{ $p->disbursed_at?->format('d/m/Y H:i') ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top:12px;font-size:11px;color:#444">Total: {{ $payouts->count() }} rows</div>
</body>
</html>