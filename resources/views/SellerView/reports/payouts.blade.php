@extends('SellerView.layouts.app_seller')

@section('content')
<div class="dashboard-header mb-5">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
        <div>
            <h2 class="fw-bold m-0 text-dark">Pencairan Dana</h2>
            <p class="text-muted">Pantau status dana jual-beli dari admin untuk setiap seller order.</p>
        </div>
        <a href="{{ route('seller.reports.index') }}" class="btn btn-light rounded-pill px-4 border fw-bold text-muted">
            <i class="bi bi-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<div class="row g-4 mb-5">
    <div class="col-md-4">
        <div class="card card-modern p-4 border-0 h-100">
            <small class="text-muted text-uppercase fw-bold ls-1 d-block mb-1">Sudah Dicairkan</small>
            <h3 class="fw-bold m-0 text-success">Rp {{ number_format($totalDisbursed, 0, ',', '.') }}</h3>
            <span class="text-muted small mt-2 d-block">Status payout DISBURSED</span>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-modern p-4 border-0 h-100">
            <small class="text-muted text-uppercase fw-bold ls-1 d-block mb-1">Siap Dicairkan</small>
            <h3 class="fw-bold m-0 text-primary">Rp {{ number_format($totalReady, 0, ',', '.') }}</h3>
            <span class="text-muted small mt-2 d-block">Menunggu admin mencairkan</span>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-modern p-4 border-0 h-100">
            <small class="text-muted text-uppercase fw-bold ls-1 d-block mb-1">Masih Tertahan</small>
            <h3 class="fw-bold m-0 text-warning">Rp {{ number_format($totalWaiting, 0, ',', '.') }}</h3>
            <span class="text-muted small mt-2 d-block">Belum delivered atau masih masa hold</span>
        </div>
    </div>
</div>

<div class="card card-modern p-4 mb-5 border-0">
    <form method="GET" class="d-flex gap-2 flex-wrap align-items-center">
        <a href="{{ route('seller.reports.payouts') }}"
           class="btn rounded-pill px-4 py-2 fw-semibold {{ !$status ? 'btn-emerald' : 'btn-light text-muted' }}">
            Semua
        </a>
        <button type="submit" name="status" value="DISBURSED"
                class="btn rounded-pill px-4 py-2 fw-semibold {{ $status === 'DISBURSED' ? 'btn-emerald' : 'btn-light text-muted' }}">
            Sudah Dicairkan
        </button>
        <button type="submit" name="status" value="READY_TO_DISBURSE"
                class="btn rounded-pill px-4 py-2 fw-semibold {{ $status === 'READY_TO_DISBURSE' ? 'btn-primary' : 'btn-light text-muted' }}">
            Siap Cair
        </button>
        <button type="submit" name="status" value="WAITING_HOLD"
                class="btn rounded-pill px-4 py-2 fw-semibold {{ $status === 'WAITING_HOLD' ? 'btn-warning text-dark' : 'btn-light text-muted' }}">
            Masa Hold
        </button>
        <button type="submit" name="status" value="WAITING_DELIVERY"
                class="btn rounded-pill px-4 py-2 fw-semibold {{ $status === 'WAITING_DELIVERY' ? 'btn-secondary' : 'btn-light text-muted' }}">
            Belum Delivered
        </button>
    </form>
</div>

<div class="card card-modern border-0 overflow-hidden">
    <div class="card-header bg-white p-4 border-bottom">
        <h5 class="fw-bold m-0 text-dark">Riwayat Pencairan Seller Order</h5>
        <small class="text-muted">Data ini mengikuti tabel payout yang juga dipakai admin.</small>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="px-4 py-3 text-muted small text-uppercase">Seller Order</th>
                    <th class="px-4 py-3 text-muted small text-uppercase">Pembeli</th>
                    <th class="px-4 py-3 text-muted small text-uppercase">Status Order</th>
                    <th class="px-4 py-3 text-muted small text-uppercase">Status Payout</th>
                    <th class="px-4 py-3 text-muted small text-uppercase text-end">Nominal</th>
                    <th class="px-4 py-3 text-muted small text-uppercase">Tanggal Cair</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($payoutOrders as $order)
                    @php
                        $payout = $order->payout;
                        $payoutStatus = $payout?->status ?? 'WAITING_DELIVERY';
                        $statusClass = match($payoutStatus) {
                            'DISBURSED' => 'bg-emerald-soft text-emerald',
                            'READY_TO_DISBURSE' => 'bg-primary-subtle text-primary',
                            'WAITING_HOLD' => 'bg-warning-subtle text-warning',
                            default => 'bg-light text-muted'
                        };
                        $statusLabel = match($payoutStatus) {
                            'DISBURSED' => 'Sudah Dicairkan',
                            'READY_TO_DISBURSE' => 'Siap Dicairkan',
                            'WAITING_HOLD' => 'Masa Hold',
                            default => 'Belum Delivered'
                        };
                    @endphp
                    <tr>
                        <td class="px-4 py-4">
                            <div class="fw-bold text-dark">{{ $order->seller_order_number ?? '#'.$order->id }}</div>
                            <small class="text-muted">{{ $order->created_at->format('d M Y, H:i') }}</small>
                        </td>
                        <td class="px-4 py-4">{{ $order->buyer->name ?? $order->buyer_name ?? '-' }}</td>
                        <td class="px-4 py-4">
                            <span class="badge bg-light text-dark rounded-pill px-3 py-2">{{ $order->status_label }}</span>
                        </td>
                        <td class="px-4 py-4">
                            <span class="badge rounded-pill px-3 py-2 {{ $statusClass }}">{{ $statusLabel }}</span>
                        </td>
                        <td class="px-4 py-4 text-end fw-bold text-dark">
                            Rp {{ number_format($payout?->amount ?? $order->subtotal, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-4">
                            @if($payout?->disbursed_at)
                                <span class="fw-semibold text-dark">{{ $payout->disbursed_at->format('d M Y') }}</span>
                                <small class="text-muted d-block">{{ $payout->disbursed_at->format('H:i') }}</small>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-end">
                            <a href="{{ route('seller.orders.show', $order->id) }}" class="btn btn-light btn-sm rounded-pill px-3 border fw-bold text-muted">
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="bi bi-wallet2 fs-1 text-muted d-block mb-3"></i>
                            <p class="text-muted mb-0">Belum ada data pencairan dana.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($payoutOrders->hasPages())
        <div class="p-4 border-top bg-light">
            {{ $payoutOrders->links() }}
        </div>
    @endif
</div>

<style>
    .ls-1 { letter-spacing: 1px; }
    .text-emerald { color: #10B981; }
    .bg-emerald-soft { background-color: #ecfdf5; }
    .bg-primary-subtle { background-color: #eff6ff; }
    .bg-warning-subtle { background-color: #fffbeb; }
</style>
@endsection
