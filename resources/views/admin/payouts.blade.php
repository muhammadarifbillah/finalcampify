@extends('layouts.admin')

@section('title', 'Audit Pencairan')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="admin-section-title text-2xl font-bold">Audit Pencairan</h1>
        <a href="{{ route('admin.disbursements.index') }}" class="px-3 py-2 bg-gray-100 rounded">Kembali ke Pencairan</a>
    </div>

    <div class="admin-card p-4">
        <form method="GET" class="flex gap-3 items-center">
            <select name="status" onchange="this.form.submit()" class="px-3 py-2 border rounded">
                <option value="">Semua Status</option>
                <option value="READY_TO_DISBURSE">Siap Dicairkan</option>
                <option value="WAITING_HOLD">Menunggu Hold</option>
                <option value="WAITING_DELIVERY">Menunggu Pengiriman</option>
                <option value="DISBURSED">Sudah Dicairkan</option>
            </select>

            <select name="source" onchange="this.form.submit()" class="px-3 py-2 border rounded">
                <option value="">Semua Sumber</option>
                <option value="auto">Auto</option>
                <option value="manual">Manual</option>
            </select>

            <input type="text" name="search" placeholder="Cari SO atau Order" class="px-3 py-2 border rounded" value="{{ request('search') }}">
            <button type="submit" class="px-3 py-2 bg-[#0f6b52] text-white rounded">Terapkan</button>
            <a href="{{ route('admin.payouts.export', request()->query()) }}" class="px-3 py-2 bg-gray-200 rounded">Ekspor Pencairan</a>
        </form>
    </div>

    <div class="admin-card">
        <div class="overflow-x-auto">
            <table class="admin-table w-full text-sm">
                <thead class="bg-[#f8fbf9] text-xs text-gray-500">
                    <tr>
                                <th class="py-3 px-3 text-left">ID</th>
                        <th class="py-3 px-3 text-left">SO / Order</th>
                        <th class="py-3 px-3 text-left">Seller</th>
                        <th class="py-3 px-3 text-right">Jumlah</th>
                        <th class="py-3 px-3 text-left">Status Pencairan</th>
                        <th class="py-3 px-3 text-left">Sumber</th>
                        <th class="py-3 px-3 text-left">Waktu Dicairkan</th>
                        <th class="py-3 px-3 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($payouts as $p)
                        <tr>
                            <td class="py-3 px-3">{{ $p->id }}</td>
                            <td class="py-3 px-3">
                                <div class="font-bold">{{ $p->sellerOrder?->seller_order_number ?? '-' }}</div>
                                <div class="text-xs">{{ $p->sellerOrder?->order?->order_number ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-3">{{ $p->sellerOrder?->store?->nama_toko ?? $p->sellerOrder?->seller?->name ?? '-' }}</td>
                            <td class="py-3 px-3 text-right">Rp {{ number_format($p->amount, 0, ',', '.') }}</td>
                            @php
                                $statusLabel = match($p->status) {
                                    'READY_TO_DISBURSE' => 'Siap Dicairkan',
                                    'WAITING_HOLD' => 'Menunggu Hold',
                                    'WAITING_DELIVERY' => 'Menunggu Pengiriman',
                                    'DISBURSED' => 'Sudah Dicairkan',
                                    default => $p->status ?? '-',
                                };
                                $sourceLabel = $p->source === 'auto' ? 'Otomatis (Scheduler)' : ($p->source === 'manual' ? 'Manual (Admin)' : ($p->source ?? '-'));
                            @endphp
                            <td class="py-3 px-3">{{ $statusLabel }}</td>
                            <td class="py-3 px-3">{{ $sourceLabel }}</td>
                            <td class="py-3 px-3">{{ $p->disbursed_at?->format('d M Y H:i') ?? '-' }}</td>
                            <td class="py-3 px-3">
                                <a href="{{ route('admin.disbursements.show', $p->seller_order_id) }}" class="text-sm text-[#0f6b52]">Detail SO</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-6 text-center text-gray-400">Tidak ada data pencairan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t bg-[#f8fbf9]">{{ $payouts->links() }}</div>
    </div>
</div>
@endsection
