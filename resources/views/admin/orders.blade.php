@extends('layouts.admin')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Orders</h1>
        <p class="text-sm text-gray-500">Semua pesanan dari pembeli, lintas seller.</p>
    </div>

    <div class="overflow-x-auto bg-white border rounded-lg">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-600">
                <tr>
                    <th class="px-4 py-3">ID</th>
                    <th class="px-4 py-3">Pembeli</th>
                    <th class="px-4 py-3">Produk</th>
                    <th class="px-4 py-3">Total</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Kurir</th>
                    <th class="px-4 py-3">Resi</th>
                    <th class="px-4 py-3">Tanggal</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($orders as $order)
                    <tr>
                        <td class="px-4 py-3 font-semibold">#{{ $order->id }}</td>
                        <td class="px-4 py-3">{{ $order->buyer->name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $order->details->pluck('product.name')->filter()->implode(', ') ?: '-' }}</td>
                        <td class="px-4 py-3">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">{{ ucfirst($order->status) }}</td>
                        <td class="px-4 py-3">{{ $order->kurir ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $order->no_resi ?? '-' }}</td>
                        <td class="px-4 py-3">{{ optional($order->created_at)->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">Belum ada order.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
