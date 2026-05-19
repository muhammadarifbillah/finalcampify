@extends('SellerView.layouts.app_seller')

@section('content')
<div class="dashboard-header mb-5">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h2 class="fw-bold m-0 text-dark">Rating & Ulasan</h2>
            <p class="text-muted">Lihat apa yang pelanggan katakan tentang produk dan layanan Anda.</p>
        </div>
        <div class="col-md-4 text-md-end">
            <div class="card card-modern p-3 border-0 text-white d-inline-block shadow-sm" style="background: linear-gradient(135deg, #111827 0%, #1f2937 100%);">
                <div class="d-flex align-items-center gap-3">
                    <div class="fs-2 text-warning">⭐</div>
                    <div class="text-start">
                        <h4 class="fw-bold m-0 text-white">{{ number_format($averageRating, 1) }}<span class="fs-6 text-white-50">/5.0</span></h4>
                        <small class="text-white-50">Rating Toko</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- PERFORMANCE SUMMARY --}}
<div class="row row-cols-2 row-cols-sm-3 row-cols-md-5 g-3 mb-5">
    @php
        $stars = [
            5 => $fiveStar,
            4 => $fourStar,
            3 => $threeStar,
            2 => $twoStar,
            1 => $oneStar
        ];
        $total = $totalReviews ?: 1;
    @endphp
    @foreach($stars as $star => $count)
    <div class="col">
        <div class="card card-modern p-3 border-0 text-center shadow-sm">
            <div class="fw-bold mb-2 text-dark">{{ $star }} <span class="text-warning">★</span></div>
            <div class="progress rounded-pill mb-2" style="height: 6px; background-color: #e2e8f0;">
                <div class="progress-bar bg-warning" style="width: {{ ($count / $total) * 100 }}%"></div>
            </div>
            <small class="text-muted" style="font-size: 0.75rem;">{{ $count }} Ulasan</small>
        </div>
    </div>
    @endforeach
</div>

{{-- RATINGS LIST --}}
<div class="row g-4">
    <div class="col-md-12">
        <h5 class="fw-bold mb-4">Ulasan Produk</h5>
        
        @forelse($productRatings as $rating)
        <div class="card card-modern p-4 mb-4 border-0 shadow-sm">
            <div class="d-flex align-items-start gap-4">
                <div class="flex-shrink-0 d-none d-sm-block">
                    <div class="p-3 bg-light rounded-circle fs-4 d-flex align-items-center justify-content-center" style="width: 55px; height: 55px;">
                        👤
                    </div>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="fw-bold m-0 text-dark">{{ $rating->user->name ?? 'Pelanggan' }}</h6>
                            <div class="text-warning my-1">
                                @for($i=1; $i<=5; $i++)
                                    <i class="bi bi-star{{ $i <= $rating->rating ? '-fill' : '' }}"></i>
                                @endfor
                            </div>
                            <small class="text-muted">Membeli: <strong>{{ $rating->product->nama_produk ?? 'Produk' }}</strong></small>
                        </div>
                        <small class="text-muted">{{ $rating->created_at->format('d M Y') }}</small>
                    </div>
                    
                    <div class="p-3 ps-5 bg-light rounded-4 mt-3 position-relative">
                        <i class="bi bi-quote position-absolute start-0 top-0 m-3 opacity-25 fs-4 text-emerald"></i>
                        <p class="m-0 text-dark leading-relaxed" style="font-style: italic;">
                            {{ $rating->ulasan ?: 'Tidak ada ulasan teks.' }}
                        </p>
                    </div>

                    @if($rating->reply)
                    <div class="ms-5 mt-3 p-3 bg-emerald-soft rounded-4" style="border-left: 4px solid var(--primary-emerald); border-top: none; border-right: none; border-bottom: none;">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-emerald text-white rounded-pill px-2" style="background: var(--primary-emerald); font-size: 0.7rem;">Penjual</span>
                            <small class="fw-bold text-muted" style="font-size: 0.8rem;">Balasan Anda:</small>
                        </div>
                        <p class="m-0 small text-dark leading-relaxed">{{ $rating->reply }}</p>
                    </div>
                    @else
                    <div class="mt-3">
                        <button class="btn btn-link text-emerald p-0 small fw-bold text-decoration-none" data-bs-toggle="collapse" data-bs-target="#replyForm{{ $rating->id }}">
                            <i class="bi bi-reply me-1"></i> Balas Ulasan
                        </button>
                        <div class="collapse mt-2" id="replyForm{{ $rating->id }}">
                            <form action="{{ route('seller.ratings.product.reply', $rating->id) }}" method="POST">
                                @csrf
                                <div class="input-group">
                                    <input type="text" name="reply" class="form-control border-0 bg-light rounded-start-4" placeholder="Tulis balasan Anda...">
                                    <button class="btn btn-emerald rounded-end-4 px-4" type="submit">Kirim</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="card card-modern p-5 text-center border-0 bg-white mb-5 shadow-sm">
            <div class="mb-4 fs-1 opacity-25">⭐</div>
            <h4 class="fw-bold">Belum Ada Ulasan Produk</h4>
            <p class="text-muted">Ulasan produk akan muncul di sini.</p>
        </div>
        @endforelse

        <h5 class="fw-bold mb-4 mt-5">Ulasan Toko</h5>
        @forelse($storeRatings as $rating)
        <div class="card card-modern p-4 mb-4 border-0 shadow-sm">
            <div class="d-flex align-items-start gap-4">
                <div class="flex-shrink-0 d-none d-sm-block">
                    <div class="p-3 bg-light rounded-circle fs-4 d-flex align-items-center justify-content-center" style="width: 55px; height: 55px;">
                        🏪
                    </div>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="fw-bold m-0 text-dark">{{ $rating->user->name ?? 'Pelanggan' }}</h6>
                            <div class="text-warning my-1">
                                @for($i=1; $i<=5; $i++)
                                    <i class="bi bi-star{{ $i <= $rating->rating ? '-fill' : '' }}"></i>
                                @endfor
                            </div>
                        </div>
                        <small class="text-muted">{{ $rating->created_at->format('d M Y') }}</small>
                    </div>
                    
                    <div class="p-3 ps-5 bg-light rounded-4 mt-3 position-relative">
                        <i class="bi bi-quote position-absolute start-0 top-0 m-3 opacity-25 fs-4 text-emerald"></i>
                        <p class="m-0 text-dark leading-relaxed" style="font-style: italic;">
                            {{ $rating->ulasan ?: 'Tidak ada ulasan teks.' }}
                        </p>
                    </div>

                    @if($rating->reply)
                    <div class="ms-5 mt-3 p-3 bg-emerald-soft rounded-4" style="border-left: 4px solid var(--primary-emerald); border-top: none; border-right: none; border-bottom: none;">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-emerald text-white rounded-pill px-2" style="background: var(--primary-emerald); font-size: 0.7rem;">Penjual</span>
                            <small class="fw-bold text-muted" style="font-size: 0.8rem;">Balasan Anda:</small>
                        </div>
                        <p class="m-0 small text-dark leading-relaxed">{{ $rating->reply }}</p>
                    </div>
                    @else
                    <div class="mt-3">
                        <button class="btn btn-link text-emerald p-0 small fw-bold text-decoration-none" data-bs-toggle="collapse" data-bs-target="#replyStoreForm{{ $rating->id }}">
                            <i class="bi bi-reply me-1"></i> Balas Ulasan Toko
                        </button>
                        <div class="collapse mt-2" id="replyStoreForm{{ $rating->id }}">
                            <form action="{{ route('seller.ratings.store.reply', $rating->id) }}" method="POST">
                                @csrf
                                <div class="input-group">
                                    <input type="text" name="reply" class="form-control border-0 bg-light rounded-start-4" placeholder="Tulis balasan untuk toko...">
                                    <button class="btn btn-emerald rounded-end-4 px-4" type="submit">Kirim</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="card card-modern p-5 text-center border-0 bg-white shadow-sm">
            <div class="mb-4 fs-1 opacity-25">🏪</div>
            <h4 class="fw-bold">Belum Ada Ulasan Toko</h4>
            <p class="text-muted">Ulasan tentang pelayanan toko Anda akan muncul di sini.</p>
        </div>
        @endforelse
    </div>
</div>

<style>
    .bg-emerald-soft { background-color: var(--soft-emerald); }
    .border-emerald { border-color: var(--primary-emerald) !important; }
</style>
@endsection
