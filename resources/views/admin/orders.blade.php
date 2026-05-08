@extends('layouts.admin')

@section('title', 'Orders Admin')

@section('content')
    <div class="space-y-8">
        <div>
            <h1 class="admin-section-title">Transaksi</h1>
            <p class="admin-section-subtitle">Semua pesanan buyer lintas seller.</p>
        </div>

        <div class="admin-card">
            <div class="flex items-center justify-between p-6">
                <h2 class="text-2xl font-extrabold">Riwayat Order</h2>
                <span class="admin-badge admin-badge-muted">Total {{ number_format($orders->count()) }}</span>
            </div>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Pembeli</th>
                            <th>Produk</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Kurir</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td class="font-extrabold">#{{ $order->id }}</td>
                                <td>{{ $order->buyer->name ?? '-' }}</td>
                                <td>{{ $order->details->pluck('product.name')->filter()->implode(', ') ?: '-' }}</td>
                                <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                <td><span class="admin-badge admin-badge-info">{{ $order->status }}</span></td>
                                <td>{{ $order->kurir ?? '-' }}</td>
                                <td>{{ $order->created_at?->format('d M Y H:i') ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7"><div class="admin-empty">Belum ada order.</div></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
