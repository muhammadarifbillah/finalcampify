<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - Campify</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.6; margin: 0; padding: 40px; }
        .header { text-align: center; border-bottom: 2px solid #10B981; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { color: #10B981; margin: 0; font-size: 28px; text-transform: uppercase; letter-spacing: 2px; }
        .header p { margin: 5px 0 0; color: #666; font-size: 14px; }
        
        .info-grid { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .info-box { font-size: 13px; }
        .info-box span { font-weight: bold; color: #555; display: block; margin-bottom: 3px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th { background-color: #f3f4f6; color: #374151; font-weight: bold; text-align: left; padding: 12px; font-size: 12px; border-bottom: 2px solid #e5e7eb; }
        td { padding: 12px; border-bottom: 1px solid #e5e7eb; font-size: 12px; }
        
        .footer { margin-top: 50px; text-align: center; font-size: 11px; color: #999; border-top: 1px solid #eee; padding-top: 20px; }
        
        .summary-box { background: #f9fafb; padding: 20px; border-radius: 12px; border: 1px solid #e5e7eb; margin-top: 20px; }
        .summary-box h3 { margin: 0 0 10px; font-size: 16px; color: #111827; }
        
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print" style="background: #fff9c4; padding: 10px; border: 1px solid #fbc02d; border-radius: 8px; margin-bottom: 20px; text-align: center; font-size: 14px;">
        💡 <strong>Tips:</strong> Klik "Cetak" dan pilih "Save as PDF" di menu printer untuk mengunduh laporan ini.
        <button onclick="window.close()" style="margin-left: 10px; cursor: pointer;">Tutup</button>
    </div>

    <div class="header">
        <h1>CAMPIFY.</h1>
        <p>Marketplace Peralatan Camping & Outdoor</p>
        <h2>{{ $title }}</h2>
    </div>

    <div class="info-grid">
        <div class="info-box">
            <span>PERIODE LAPORAN</span>
            {{ date('d M Y', strtotime($startDate)) }} - {{ date('d M Y', strtotime($endDate)) }}
        </div>
        <div class="info-box" style="text-align: right;">
            <span>DICETAK PADA</span>
            {{ date('d M Y, H:i') }}
        </div>
    </div>

    @if($type === 'sales')
        <table>
            <thead>
                <tr>
                    <th>TANGGAL</th>
                    <th>ORDER ID</th>
                    <th>PEMBELI</th>
                    <th>PRODUK</th>
                    <th>QTY</th>
                    <th>TOTAL HARGA</th>
                </tr>
            </thead>
            <tbody>
                @php $totalAmount = 0; @endphp
                @foreach($data as $order)
                    @foreach($order->details as $detail)
                        @php $totalAmount += ($detail->harga * $detail->qty); @endphp
                        <tr>
                            <td>{{ $order->created_at->format('d/m/Y') }}</td>
                            <td>#{{ $order->id }}</td>
                            <td>{{ $order->buyer->name ?? 'User' }}</td>
                            <td>{{ $detail->product->nama_produk ?? 'Produk' }}</td>
                            <td>{{ $detail->qty }}</td>
                            <td>Rp {{ number_format($detail->harga * $detail->qty) }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="5" style="text-align: right;">TOTAL PENDAPATAN</th>
                    <th style="font-size: 14px;">Rp {{ number_format($totalAmount) }}</th>
                </tr>
            </tfoot>
        </table>
    @else
        <table>
            <thead>
                <tr>
                    <th>TANGGAL</th>
                    <th>ID</th>
                    <th>PENYEWA</th>
                    <th>PRODUK</th>
                    <th>SEWA KOTOR</th>
                    <th>ADMIN (10%)</th>
                    <th>BERSIH</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $totalGross = 0; 
                    $totalAdmin = 0;
                    $totalNet = 0;
                @endphp
                @foreach($data as $rental)
                    @php 
                        $gross = $rental->price * $rental->duration;
                        $admin = $gross * 0.1;
                        $net = $gross - $admin;
                        
                        $totalGross += $gross; 
                        $totalAdmin += $admin;
                        $totalNet += $net;
                    @endphp
                    <tr>
                        <td>{{ $rental->created_at->format('d/m/Y') }}</td>
                        <td>#{{ $rental->id }}</td>
                        <td>{{ $rental->user->name ?? 'User' }}</td>
                        <td>{{ $rental->product->nama_produk ?? 'Produk' }}</td>
                        <td>Rp {{ number_format($gross) }}</td>
                        <td style="color: #dc2626;">-Rp {{ number_format($admin) }}</td>
                        <td style="font-weight: bold;">Rp {{ number_format($net) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="6" style="text-align: right;">TOTAL POTONGAN ADMIN</th>
                    <th style="color: #dc2626;">Rp {{ number_format($totalAdmin) }}</th>
                </tr>
                <tr>
                    <th colspan="6" style="text-align: right; font-size: 14px;">TOTAL PENDAPATAN BERSIH</th>
                    <th style="font-size: 14px; background: #ecfdf5; color: #065f46;">Rp {{ number_format($totalNet) }}</th>
                </tr>
            </tfoot>
        </table>
    @endif

    <div class="summary-box">
        <h3>Pernyataan Laporan</h3>
        <p style="font-size: 12px; color: #4b5563; margin: 0;">Laporan ini dihasilkan secara otomatis oleh sistem Campify Seller Hub. Segala data transaksi yang tertera adalah valid sesuai dengan status transaksi "Selesai" di platform kami.</p>
    </div>

    <div class="footer">
        &copy; {{ date('Y') }} Campify Marketplace. All rights reserved.
    </div>
</body>
</html>
