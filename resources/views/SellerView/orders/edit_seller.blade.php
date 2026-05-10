@extends('SellerView.layouts.app_seller')

@section('content')
<div class="dashboard-header mb-5">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h2 class="fw-bold m-0 text-dark">Ubah Status & Resi</h2>
            <p class="text-muted">Perbarui progres pengiriman pesanan pelanggan Anda.</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('seller.orders.index') }}" class="btn btn-light rounded-pill px-4 border shadow-sm">
                <i class="bi bi-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card card-modern border-0 shadow-sm p-4" style="border-radius:16px;">
            <div class="mb-4 pb-3 border-bottom">
                <h5 class="fw-bold mb-1 text-dark">{{ $order->buyer_name }}</h5>
                <p class="text-muted small mb-0">Produk: <strong class="text-emerald">{{ optional($order->product)->nama_produk ?? '-' }}</strong></p>
            </div>

            <form method="POST" action="/seller/orders/{{ $order->id }}">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="form-label fw-bold text-muted small text-uppercase ls-1">Status Pesanan</label>
                    <select name="status" class="form-select border-0 bg-light rounded-3 px-3 py-2 shadow-sm">
                        <option value="menunggu" {{ $order->status == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                        <option value="diproses" {{ $order->status == 'diproses' ? 'selected' : '' }}>Diproses</option>
                        <option value="dikirim" {{ $order->status == 'dikirim' ? 'selected' : '' }}>Dikirim</option>
                        <option value="selesai" {{ $order->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="dibatalkan" {{ $order->status == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold text-muted small text-uppercase ls-1">Nomor Resi Pengiriman</label>
                    <input type="text" name="resi" class="form-control border-0 bg-light rounded-3 px-3 py-2 shadow-sm" 
                           value="{{ $order->resi ?? '' }}" 
                           placeholder="Masukkan nomor resi pengiriman">
                </div>

                <div class="mt-4">
                    <button class="btn btn-emerald text-white rounded-pill px-5 py-2 fw-bold shadow-sm">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-modern border-0 p-4 bg-emerald text-white shadow-sm">
            <h6 class="fw-bold mb-3"><i class="bi bi-info-circle me-2"></i>Panduan Update</h6>
            <p class="small mb-2 opacity-75">1. Pastikan barang sudah siap sebelum mengubah ke status <strong>Diproses</strong>.</p>
            <p class="small mb-2 opacity-75">2. Masukkan nomor resi yang valid saat status diubah ke <strong>Dikirim</strong>.</p>
            <p class="small mb-0 opacity-75">3. Gunakan fitur chat untuk memberi tahu pembeli jika ada kendala.</p>
        </div>
    </div>
</div>

<style>
    .ls-1 { letter-spacing: 1px; }
    .text-emerald { color: #10B981 !important; }
    .bg-emerald { background-color: #10B981 !important; }
</style>
@endsection
