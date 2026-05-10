@extends('layouts.admin')

@section('title', 'Return Detail')

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
    <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
        <div>
            <h1 class="admin-section-title">Return #{{ $return->id }}</h1>
            <p class="admin-section-subtitle">Order ID: <span class="font-extrabold">{{ $return->order_id }}</span></p>
        </div>
        <div class="flex items-center gap-2">
            <span class="admin-badge {{ $badgeMap[$return->status] ?? 'admin-badge-muted' }}">{{ $return->status }}</span>
            <a class="admin-button admin-button-ghost" href="{{ route('admin.returns.index') }}">Kembali</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-7 space-y-6">
            <div class="admin-card p-6 space-y-3">
                <h2 class="text-xl font-extrabold">Ringkasan</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <div class="text-xs font-extrabold tracking-widest uppercase text-slate-500">Buyer</div>
                        <div class="text-lg font-bold">{{ $return->order?->user?->name ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-extrabold tracking-widest uppercase text-slate-500">Tipe</div>
                        <div class="text-lg font-bold">{{ $return->type }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-extrabold tracking-widest uppercase text-slate-500">Escrow Total</div>
                        <div class="text-lg font-extrabold">Rp {{ number_format((int) $return->escrow_total) }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-extrabold tracking-widest uppercase text-slate-500">Late Fee</div>
                        <div class="text-lg font-extrabold text-rose-700">Rp {{ number_format((int) $return->late_fee) }}</div>
                    </div>
                </div>
            </div>

            <div class="admin-card">
                <div class="p-6 border-b border-slate-200 flex items-center justify-between">
                    <h2 class="text-xl font-extrabold">Produk di Order</h2>
                    <span class="admin-badge admin-badge-muted">{{ $return->order?->details?->count() ?? 0 }} Item</span>
                </div>
                <div class="p-6 space-y-4">
                    @forelse(($return->order?->details ?? []) as $detail)
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <div class="font-extrabold">{{ $detail->product?->name ?? 'Produk' }}</div>
                                <div class="text-sm text-slate-500">Type: {{ $detail->type }} • Qty: {{ $detail->qty }}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-extrabold">Rp {{ number_format((int) $detail->harga) }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="admin-empty">Tidak ada order detail.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="lg:col-span-5 space-y-6">
            <div class="admin-card p-6">
                <h2 class="text-xl font-extrabold mb-4">Update Data</h2>

                <form method="POST" action="{{ route('admin.returns.update', $return->id) }}" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-extrabold tracking-widest uppercase text-slate-500 mb-2" for="type">Type</label>
                            <select class="admin-form-control" id="type" name="type">
                                @foreach($types as $type)
                                    <option value="{{ $type }}" @selected($return->type === $type)>{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-extrabold tracking-widest uppercase text-slate-500 mb-2" for="status">Status</label>
                            <select class="admin-form-control" id="status" name="status">
                                @foreach($statuses as $status)
                                    <option value="{{ $status }}" @selected($return->status === $status)>{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold tracking-widest uppercase text-slate-500 mb-2" for="escrow_total">Escrow Total</label>
                        <input class="admin-form-control" id="escrow_total" name="escrow_total" value="{{ old('escrow_total', (int) $return->escrow_total) }}" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-extrabold tracking-widest uppercase text-slate-500 mb-2" for="expected_date">Expected Date</label>
                            <input class="admin-form-control" type="datetime-local" id="expected_date" name="expected_date"
                                   value="{{ old('expected_date', $return->expected_date?->format('Y-m-d\\TH:i')) }}" />
                        </div>
                        <div>
                            <label class="block text-xs font-extrabold tracking-widest uppercase text-slate-500 mb-2" for="actual_date">Actual Date</label>
                            <input class="admin-form-control" type="datetime-local" id="actual_date" name="actual_date"
                                   value="{{ old('actual_date', $return->actual_date?->format('Y-m-d\\TH:i')) }}" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold tracking-widest uppercase text-slate-500 mb-2" for="damage_fee">Damage Fee</label>
                        <input class="admin-form-control" id="damage_fee" name="damage_fee" value="{{ old('damage_fee', (int) $return->damage_fee) }}" />
                    </div>

                    <button class="admin-button admin-button-primary w-full" type="submit">Simpan</button>
                </form>
            </div>

            <div class="admin-card p-6 space-y-4">
                <h2 class="text-xl font-extrabold">Settlement</h2>

                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 rounded-lg bg-slate-50 border border-slate-200">
                        <div class="text-xs font-extrabold tracking-widest uppercase text-slate-500">To Seller</div>
                        <div class="text-lg font-extrabold">Rp {{ number_format((int) $return->to_seller) }}</div>
                    </div>
                    <div class="p-4 rounded-lg bg-slate-50 border border-slate-200">
                        <div class="text-xs font-extrabold tracking-widest uppercase text-slate-500">To Buyer</div>
                        <div class="text-lg font-extrabold">Rp {{ number_format((int) $return->to_buyer) }}</div>
                    </div>
                </div>

                <div class="flex gap-3">
                    <form method="POST" action="{{ route('admin.returns.finalize', $return->id) }}" class="flex-1">
                        @csrf
                        <input type="hidden" name="final_status" value="completed" />
                        <button class="admin-button admin-button-primary w-full" type="submit">Set Completed</button>
                    </form>
                    <form method="POST" action="{{ route('admin.returns.finalize', $return->id) }}" class="flex-1">
                        @csrf
                        <input type="hidden" name="final_status" value="rejected" />
                        <button class="admin-button admin-button-danger w-full" type="submit">Set Rejected</button>
                    </form>
                </div>

                <p class="text-xs text-slate-500">
                    Catatan: tombol finalize akan menghitung ulang late fee dan pembagian dana berdasarkan data yang tersimpan.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

