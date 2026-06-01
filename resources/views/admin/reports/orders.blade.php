 @extends('admin.reports.layout')

@section('title', 'Laporan Transaksi Pemesanan - CAMPIFY')
@section('report_title', 'Laporan Transaksi Pemesanan')

@section('content')
    {{-- Summary Cards --}}
    <table class="stat-boxes" cellpadding="0" cellspacing="8">
        <tr>
            <td class="stat-box" width="33%">
                <div class="stat-value">{{ count($orders) }}</div>
                <div class="stat-label">Total Pesanan</div>
            </td>
            <td class="stat-box" width="33%">
                <div class="stat-value" style="color: #059669;">Rp {{ number_format($orders->sum('total'), 0, ',', '.') }}</div>
                <div class="stat-label">Total Nominal (Gross)</div>
            </td>
            <td class="stat-box" width="33%">
                <div class="stat-value" style="color: #d97706;">Rp {{ number_format($orders->sum('total') * 0.1, 0, ',', '.') }}</div>
                <div class="stat-label">Pendapatan Platform (10% Fee)</div>
            </td>
        </tr>
    </table>

    {{-- Main Data Table --}}
    <div class="section-title">Rincian Transaksi Pemesanan</div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="12%">No. Order</th>
                <th width="15%">Tanggal</th>
                <th width="18%">Pembeli</th>
                <th width="18%">Toko/Seller</th>
                <th width="10%">Tipe</th>
                <th width="15%" class="text-right">Total Price (Rp)</th>
                <th width="12%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td class="text-bold">{{ $order->order_number ?? '#' . $order->id }}</td>
                    <td>{{ $order->created_at ? $order->created_at->format('d/m/Y H:i') : '-' }}</td>
                    <td>
                        {{ $order->buyer->name ?? 'User #' . $order->user_id }}<br>
                        <span style="font-size: 6.5pt; color: #475569;">{{ $order->buyer->email ?? '' }}</span>
                    </td>
                    <td>
                        @php
                            $firstDetail = $order->details->first();
                            $storeName = $firstDetail && $firstDetail->product && $firstDetail->product->store 
                                ? $firstDetail->product->store->nama_toko 
                                : 'Toko N/A';
                        @endphp
                        {{ $storeName }}
                    </td>
                    <td>
                        @php
                            $isRental = $order->details->where('type', 'rent')->isNotEmpty();
                        @endphp
                        @if($isRental)
                            <span class="badge badge-blue">Sewa</span>
                        @else
                            <span class="badge badge-green">Beli</span>
                        @endif
                    </td>
                    <td class="text-right text-bold">
                        Rp {{ number_format($order->total, 0, ',', '.') }}
                    </td>
                    <td>
                        @switch(strtolower($order->status))
                            @case('completed')
                            @case('selesai')
                                <span class="badge badge-green">Selesai</span>
                                @break
                            @case('pending')
                                <span class="badge badge-yellow">Pending</span>
                                @break
                            @case('cancelled')
                            @case('batal')
                                <span class="badge badge-red">Batal</span>
                                @break
                            @case('shipping')
                            @case('dikirim')
                                <span class="badge badge-blue">Dikirim</span>
                                @break
                            @default
                                <span class="badge badge-gray">{{ $order->status }}</span>
                        @endswitch
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 15px; color: #475569;">
                        Tidak ada data transaksi pemesanan pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Detail Barang --}}
    @if(count($orders) > 0)
        <div class="page-break"></div>
        <div class="section-title" style="margin-top: 0;">Rincian Item dalam Pesanan</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th width="12%">No. Order</th>
                    <th width="35%">Nama Produk</th>
                    <th width="10%" class="text-center">Jumlah</th>
                    <th width="15%" class="text-right">Harga Satuan (Rp)</th>
                    <th width="15%" class="text-right">Subtotal (Rp)</th>
                    <th width="13%" class="text-center">Tipe Transaksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                    @foreach($order->details as $detail)
                        <tr>
                            <td class="text-bold">{{ $order->order_number ?? '#' . $order->id }}</td>
                            <td>
                                {{ $detail->product->name ?? 'Produk #' . $detail->product_id }}<br>
                                <span style="font-size: 6.5pt; color: #475569;">
                                    Toko: {{ $detail->product->store->nama_toko ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="text-center">{{ $detail->qty }}</td>
                            <td class="text-right">Rp {{ number_format($detail->harga, 0, ',', '.') }}</td>
                            <td class="text-right text-bold">Rp {{ number_format($detail->harga * $detail->qty, 0, ',', '.') }}</td>
                            <td class="text-center">
                                @if($detail->type === 'rent')
                                    <span style="color: #2563eb; font-size: 7.5pt;">Sewa</span>
                                @else
                                    <span style="color: #059669; font-size: 7.5pt;">Beli</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
