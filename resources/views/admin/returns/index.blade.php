@extends('layouts.admin')

@section('title', 'Returns')

@section('content')
@php
    $badgeMap = [
        'pending' => 'admin-badge-warning',
        'dispute' => 'admin-badge-danger',
        'checking' => 'admin-badge-info',
        'completed' => 'admin-badge-success',
        'rejected' => 'admin-badge-muted',
    ];
@endphp

<div class="space-y-8">
    <div>
        <h1 class="admin-section-title">Return & Escrow Resolution</h1>
        <p class="admin-section-subtitle">Kelola retur jual-beli dan pengembalian sewa dalam satu modul.</p>
    </div>

    <div class="admin-card p-6">
        <form method="GET" action="{{ route('admin.returns.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
            <div>
                <label class="block text-xs font-extrabold tracking-widest uppercase text-slate-500 mb-2" for="type">Tipe</label>
                <select class="admin-form-control" id="type" name="type">
                    <option value="">Semua</option>
                    @foreach($types as $type)
                        <option value="{{ $type }}" @selected(request('type') === $type)>{{ $type }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-extrabold tracking-widest uppercase text-slate-500 mb-2" for="status">Status</label>
                <select class="admin-form-control" id="status" name="status">
                    <option value="">Semua</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-extrabold tracking-widest uppercase text-slate-500 mb-2" for="order_id">Order ID</label>
                <input class="admin-form-control" id="order_id" name="order_id" value="{{ request('order_id') }}" placeholder="cth: 123" />
            </div>

            <div>
                <label class="block text-xs font-extrabold tracking-widest uppercase text-slate-500 mb-2" for="from">Dari</label>
                <input class="admin-form-control" type="date" id="from" name="from" value="{{ request('from') }}" />
            </div>

            <div>
                <label class="block text-xs font-extrabold tracking-widest uppercase text-slate-500 mb-2" for="to">Sampai</label>
                <input class="admin-form-control" type="date" id="to" name="to" value="{{ request('to') }}" />
            </div>

            <div class="flex gap-2 justify-end">
                <button class="admin-button admin-button-primary" type="submit">Filter</button>
                <a class="admin-button admin-button-ghost" href="{{ route('admin.returns.index') }}">Reset</a>
            </div>
        </form>
    </div>

    <div class="admin-card">
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Order</th>
                        <th>User</th>
                        <th>Tipe</th>
                        <th>Status</th>
                        <th class="text-right">Escrow</th>
                        <th class="text-right">To Seller</th>
                        <th class="text-right">To Buyer</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($returns as $item)
                        <tr>
                            <td class="font-extrabold">#{{ $item->id }}</td>
                            <td>{{ $item->order_id }}</td>
                            <td>{{ $item->order?->user?->name ?? '-' }}</td>
                            <td>{{ $item->type }}</td>
                            <td>
                                <span class="admin-badge {{ $badgeMap[$item->status] ?? 'admin-badge-muted' }}">{{ $item->status }}</span>
                            </td>
                            <td class="text-right">Rp {{ number_format((int) $item->escrow_total) }}</td>
                            <td class="text-right">Rp {{ number_format((int) $item->to_seller) }}</td>
                            <td class="text-right">Rp {{ number_format((int) $item->to_buyer) }}</td>
                            <td class="text-right">
                                <a class="admin-button admin-button-ghost" href="{{ route('admin.returns.show', $item->id) }}">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9"><div class="admin-empty">Tidak ada data return.</div></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-6">
            {{ $returns->links() }}
        </div>
    </div>
</div>
@endsection
