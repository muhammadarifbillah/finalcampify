@extends('SellerView.layouts.app_seller')

@section('content')
<div class="dashboard-header mb-5">
    <div class="d-flex align-items-center">
        <a href="{{ route('seller.products.index') }}" class="btn btn-light rounded-circle p-3 me-4 shadow-sm border-0 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
            <i class="bi bi-arrow-left fs-4"></i>
        </a>
        <div>
            <h2 class="fw-bold m-0 text-dark">Detail Produk</h2>
            <p class="text-muted">Kelola dan lihat informasi lengkap unit barang Anda.</p>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- LEFT: PRODUCT IMAGE & QUICK STATS --}}
    <div class="col-lg-5">
        <div class="card card-modern border-0 overflow-hidden mb-4 shadow-lg">
            <div class="position-relative" style="height: 400px; background: #f8fafc;">
                @if($product->gambar && file_exists(public_path('storage/'.$product->gambar)))
                    <img src="https://images.unsplash.com/photo-1523987355523-c7b5b0dd90a7?q=80&w=1200&auto=format&fit=crop" class="w-100 h-100 object-fit-cover" alt="{{ $product->nama_produk }}">
                @else
                    <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-muted opacity-25">
                        <i class="bi bi-image fs-1"></i>
                        <span class="mt-2">No Image Available</span>
                    </div>
                @endif

                <div class="position-absolute top-0 start-0 p-3">
                    @if($product->jenis_produk == 'sewa')
                        <span class="badge bg-primary rounded-pill px-3 py-2 shadow">SEWA ALAT</span>
                    @else
                        <span class="badge bg-success rounded-pill px-3 py-2 shadow">JUAL BARANG</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-6">
                <div class="card card-modern border-0 p-4 text-center">
                    <small class="text-muted text-uppercase fw-bold ls-1 d-block mb-1" style="font-size: 0.7rem;">Stok Tersedia</small>
                    <h3 class="fw-bold m-0 {{ $product->stok > 0 ? 'text-dark' : 'text-danger' }}">{{ $product->stok }}</h3>
                </div>
            </div>
            <div class="col-6">
                <div class="card card-modern border-0 p-4 text-center">
                    <small class="text-muted text-uppercase fw-bold ls-1 d-block mb-1" style="font-size: 0.7rem;">Total Terjual</small>
                    <h3 class="fw-bold m-0 text-emerald">{{ $product->orders->count() ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT: PRODUCT INFO & ACTIONS --}}
    <div class="col-lg-7">
        <div class="card card-modern border-0 p-5 mb-4 h-100">
            <div class="mb-4">
                <span class="badge bg-emerald-soft text-emerald rounded-pill px-3 py-2 fw-bold text-uppercase ls-1 mb-2">
                    {{ ucfirst($product->kategori) }}
                </span>
                <h1 class="fw-bold text-dark mt-2 mb-3">{{ $product->nama_produk }}</h1>
                
                <div class="d-flex align-items-center gap-2 mb-4">
                    <div class="text-warning fs-5">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="bi bi-star{{ $i <= round($avgProductRating) ? '-fill' : '' }}"></i>
                        @endfor
                    </div>
                    <span class="fw-bold text-dark">{{ number_format($avgProductRating, 1) }}</span>
                    <span class="text-muted small">({{ $productRatingCount }} Ulasan Pelanggan)</span>
                </div>

                <h2 class="fw-bold text-emerald mb-4">
                    Rp {{ number_format($product->harga, 0, ',', '.') }}
                    @if($product->jenis_produk == 'sewa')
                        <span class="fs-5 text-muted fw-normal">/hari</span>
                    @endif
                </h2>

                <div class="p-4 bg-light rounded-4 mb-4">
                    <h6 class="fw-bold text-muted small text-uppercase ls-1 mb-3">Deskripsi Produk</h6>
                    <p class="text-dark m-0 leading-relaxed">{{ $product->deskripsi }}</p>
                </div>
            </div>

            <div class="mt-auto pt-4 border-top">
                <div class="d-flex gap-3">
                    <a href="/seller/products/{{ $product->id }}/edit" class="btn btn-emerald flex-grow-1 py-3 rounded-4 fw-bold shadow-sm">
                        <i class="bi bi-pencil-square me-2"></i>Edit Informasi Produk
                    </a>
                    <form action="{{ route('seller.products.destroy', $product->id) }}" method="POST" class="flex-grow-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100 py-3 rounded-4 fw-bold" onclick="return confirm('Yakin ingin menghapus produk ini?')">
                            <i class="bi bi-trash me-2"></i>Hapus Produk
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- REVIEWS SECTION --}}
<div class="row g-4 mt-2">
    <div class="col-md-6">
        <div class="card card-modern border-0 p-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold m-0">Ulasan Produk</h5>
                <span class="badge bg-light text-muted rounded-pill">{{ $productRatings->count() }} Ulasan</span>
            </div>
            
            <div class="review-scroll" style="max-height: 400px; overflow-y: auto;">
                @forelse($productRatings as $pr)
                <div class="mb-4 pb-4 border-bottom last-border-0">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-sm bg-emerald text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 35px; height: 35px; font-size: 0.8rem;">
                                {{ strtoupper(substr($pr->user->name ?? 'U', 0, 1)) }}
                            </div>
                            <span class="fw-bold text-dark">{{ $pr->user->name ?? 'User' }}</span>
                        </div>
                        <small class="text-muted">{{ $pr->created_at->diffForHumans() }}</small>
                    </div>
                    <div class="text-warning mb-2" style="font-size: 0.8rem;">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="bi bi-star{{ $i <= $pr->rating ? '-fill' : '' }}"></i>
                        @endfor
                    </div>
                    <p class="text-muted small m-0 fst-italic">"{{ $pr->ulasan ?? 'Tanpa ulasan teks' }}"</p>
                </div>
                @empty
                <div class="text-center py-5 opacity-25">
                    <i class="bi bi-chat-left-dots fs-1 d-block mb-3"></i>
                    <p>Belum ada ulasan produk.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card card-modern border-0 p-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold m-0">Rating Toko</h5>
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-bold text-warning">{{ number_format($avgStoreRating, 1) }}</span>
                    <div class="text-warning" style="font-size: 0.8rem;">
                        <i class="bi bi-star-fill"></i>
                    </div>
                </div>
            </div>

            <div class="review-scroll" style="max-height: 400px; overflow-y: auto;">
                @forelse($storeRatings as $sr)
                <div class="mb-4 pb-4 border-bottom last-border-0">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="fw-bold text-dark">{{ $sr->user->name ?? 'User' }}</span>
                        <small class="text-muted">{{ $sr->created_at->diffForHumans() }}</small>
                    </div>
                    <div class="text-warning mb-2" style="font-size: 0.8rem;">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="bi bi-star{{ $i <= $sr->rating ? '-fill' : '' }}"></i>
                        @endfor
                    </div>
                    <p class="text-muted small m-0 fst-italic">"{{ $sr->ulasan ?? 'Tanpa ulasan teks' }}"</p>
                </div>
                @empty
                <div class="text-center py-5 opacity-25">
                    <i class="bi bi-shop fs-1 d-block mb-3"></i>
                    <p>Belum ada rating toko.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<style>
    .ls-1 { letter-spacing: 1px; }
    .bg-emerald-soft { background-color: #ecfdf5; }
    .object-fit-cover { object-fit: cover; }
    .leading-relaxed { line-height: 1.6; }
    .last-border-0:last-child { border-bottom: none !important; margin-bottom: 0 !important; padding-bottom: 0 !important; }
    .review-scroll::-webkit-scrollbar { width: 5px; }
    .review-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>
@endsection
