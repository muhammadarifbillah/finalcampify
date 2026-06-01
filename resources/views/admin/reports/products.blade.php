@extends('admin.reports.layout')

@section('title', 'Laporan Data Produk - CAMPIFY')
@section('report_title', 'Laporan Data Produk')

@section('content')
    {{-- Summary Cards --}}
    <table class="stat-boxes" cellpadding="0" cellspacing="8">
        <tr>
            <td class="stat-box" width="25%">
                <div class="stat-value">{{ count($products) }}</div>
                <div class="stat-label">Total Produk</div>
            </td>
            <td class="stat-box" width="25%">
                <div class="stat-value" style="color: #059669;">{{ $products->where('status', 'approved')->count() }}</div>
                <div class="stat-label">Approved</div>
            </td>
            <td class="stat-box" width="25%">
                <div class="stat-value" style="color: #d97706;">{{ $products->whereIn('status', ['waiting', 'pending'])->count() }}</div>
                <div class="stat-label">Waiting / Pending</div>
            </td>
            <td class="stat-box" width="25%">
                <div class="stat-value" style="color: #dc2626;">{{ $products->where('status', 'rejected')->count() }}</div>
                <div class="stat-label">Rejected</div>
            </td>
        </tr>
    </table>

    {{-- Main Data Table --}}
    <div class="section-title">Daftar Produk Platform</div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">ID</th>
                <th width="28%">Nama Produk</th>
                <th width="18%">Toko / Seller</th>
                <th width="12%">Kategori</th>
                <th width="15%" class="text-right">Harga (Rp)</th>
                <th width="7%" class="text-center">Stok</th>
                <th width="8%">Tipe</th>
                <th width="7%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $p)
                <tr>
                    <td class="text-bold">#{{ $p->id }}</td>
                    <td>
                        <span class="text-bold">{{ $p->name }}</span>
                    </td>
                    <td>
                        {{ $p->store->nama_toko ?? '-' }}
                    </td>
                    <td>
                        {{ $p->category ?? $p->kategori ?? '-' }}
                    </td>
                    <td class="text-right text-bold">
                        Rp {{ number_format($p->price ?? 0, 0, ',', '.') }}
                    </td>
                    <td class="text-center">{{ $p->stok ?? $p->stock ?? 0 }}</td>
                    <td>
                        @if($p->is_rental || strtolower($p->jenis_produk ?? '') === 'sewa' || ($p->rent_price ?? 0) > 0)
                            <span class="badge badge-blue">Sewa</span>
                        @else
                            <span class="badge badge-green">Beli</span>
                        @endif
                    </td>
                    <td>
                        @switch(strtolower($p->status ?? ''))
                            @case('approved')
                                <span class="badge badge-green">Approved</span>
                                @break
                            @case('waiting')
                            @case('pending')
                                <span class="badge badge-yellow">Waiting</span>
                                @break
                            @case('rejected')
                                <span class="badge badge-red">Rejected</span>
                                @break
                            @default
                                <span class="badge badge-gray">{{ $p->status }}</span>
                        @endswitch
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 15px; color: #64748b;">
                        Tidak ada data produk.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
