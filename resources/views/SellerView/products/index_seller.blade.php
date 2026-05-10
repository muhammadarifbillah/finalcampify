@extends('SellerView.layouts.app_seller')

@section('content')
<div class="dashboard-header mb-5">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h2 class="fw-bold m-0 text-dark">Katalog Produk</h2>
            <p class="text-muted">Kelola inventaris barang jual dan sewa Anda di sini.</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('seller.products.create') }}" class="btn btn-emerald px-4 shadow-sm">
                <i class="bi bi-plus-circle me-2"></i>Tambah Produk Baru
            </a>
        </div>
    </div>
</div>

{{-- FILTER BUTTONS --}}
<div class="card card-modern p-3 mb-5 border-0">
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('seller.products.index') }}" 
           class="btn rounded-pill px-4 py-2 fw-semibold {{ request('jenis') == null ? 'btn-emerald' : 'btn-light text-muted' }}">
           Semua Produk
        </a>
        <a href="{{ route('seller.products.index', ['jenis' => 'sewa']) }}" 
           class="btn rounded-pill px-4 py-2 fw-semibold {{ request('jenis') == 'sewa' ? 'btn-emerald' : 'btn-light text-muted' }}">
           Penyewaan Alat
        </a>
        <a href="{{ route('seller.products.index', ['jenis' => 'jual']) }}" 
           class="btn rounded-pill px-4 py-2 fw-semibold {{ request('jenis') == 'jual' ? 'btn-emerald' : 'btn-light text-muted' }}">
           Penjualan Barang
        </a>
    </div>
</div>

{{-- PRODUK GRID --}}
<div class="row g-4">
    @forelse ($products as $product)
    <div class="col-xl-4 col-md-6">
        <div class="card card-modern h-100 border-0 overflow-hidden">
            
            {{-- IMAGE SECTION --}}
            <div class="position-relative" style="height: 240px;">
                @if($product->gambar && file_exists(public_path('storage/'.$product->gambar)))
                    <img src="{{ asset('storage/'.$product->gambar) }}" class="w-100 h-100 object-fit-cover" alt="{{ $product->nama_produk }}">
                @else
                    <div class="w-100 h-100 bg-light d-flex flex-column align-items-center justify-content-center">
                        <span class="fs-1 opacity-25">🏕️</span>
                        <small class="text-muted mt-2">No Image Preview</small>
                    </div>
                @endif

                {{-- BADGES OVERLAY --}}
                <div class="position-absolute top-0 start-0 p-3 d-flex flex-column gap-2">
                    @if($product->jenis_produk == 'sewa')
                        <span class="badge bg-primary rounded-pill px-3 py-2 shadow-sm border border-white border-2">SEWA ALAT</span>
                    @else
                        <span class="badge bg-success rounded-pill px-3 py-2 shadow-sm border border-white border-2">JUAL BARANG</span>
                    @endif
                </div>

                <div class="position-absolute bottom-0 end-0 p-3">
                    @if($product->status === 'approved')
                        <span class="badge bg-emerald text-white rounded-pill px-3 py-2 shadow-sm" style="background: var(--primary-emerald);">Aktif</span>
                    @elseif($product->status === 'rejected')
                        <span class="badge bg-danger rounded-pill px-3 py-2 shadow-sm">Ditolak</span>
                    @else
                        <span class="badge bg-warning text-dark rounded-pill px-3 py-2 shadow-sm">Menunggu Review</span>
                    @endif
                </div>
            </div>

            <div class="card-body p-4 d-flex flex-column">
                <div class="mb-3">
                    <small class="text-emerald fw-bold text-uppercase ls-1" style="font-size: 0.75rem;">
                        {{ ucfirst(str_replace('_',' ',$product->kategori ?? 'Umum')) }}
                    </small>
                    <h5 class="fw-bold mt-1 mb-2">{{ $product->nama_produk }}</h5>
                    <p class="text-muted small mb-0">{{ Str::limit($product->deskripsi, 80) }}</p>
                </div>

                <div class="mt-auto pt-3 border-top">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h4 class="fw-bold text-emerald m-0">
                                Rp {{ number_format($product->harga, 0, ',', '.') }}
                                @if($product->jenis_produk == 'sewa')
                                    <span class="fs-6 text-muted fw-normal">/hari</span>
                                @endif
                            </h4>
                        </div>
                        <div class="text-end">
                            <small class="text-muted d-block">Stok</small>
                            <span class="fw-bold {{ $product->stok > 0 ? 'text-dark' : 'text-danger' }}">
                                {{ $product->stok ?? 0 }} Unit
                            </span>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="/seller/products/{{ $product->id }}/edit" class="btn btn-light rounded-3 flex-grow-1 fw-bold text-muted border">
                            <i class="bi bi-pencil-square me-2"></i>Edit
                        </a>
                        <form action="{{ route('seller.products.destroy', $product->id) }}" method="POST" class="flex-grow-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger rounded-3 w-100 fw-bold" onclick="return confirm('Yakin ingin menghapus produk ini?')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card card-modern p-5 text-center border-0 bg-white">
            <div class="mb-4 fs-1 opacity-25">📦</div>
            <h4 class="fw-bold">Belum Ada Produk Terdaftar</h4>
            <p class="text-muted">Mulailah berjualan dengan menambahkan produk pertama Anda ke katalog.</p>
            <div class="mt-4">
                <a href="{{ route('seller.products.create') }}" class="btn btn-emerald px-5 py-3">
                    Tambah Produk Sekarang
                </a>
            </div>
        </div>
    </div>
    @endforelse
</div>

<style>
    .ls-1 { letter-spacing: 1px; }
    .text-emerald { color: var(--primary-emerald); }
    .object-fit-cover { object-fit: cover; }
</style>
@endsection
