@extends('SellerView.layouts.app_seller')

@section('content')
<div class="d-flex" style="min-height:100vh; background:#f9fafb;">
     {{-- SIDEBAR --}}
    <div style="width:260px; background:#ffffff; border-right:1px solid #e5e7eb; display:flex; flex-direction:column; justify-content:space-between;">

        {{-- TOP --}}
        <div>

            {{-- BRAND --}}
            <div class="p-4 border-bottom">
                <h4 style="color:#10B981; font-weight:800; letter-spacing:1px;">CAMPIFY.</h4>
                <small class="text-muted">SELLER HUB</small>
            </div>

            {{-- MENU --}}
            <ul class="nav flex-column px-3 mt-3">

                {{-- DASHBOARD --}}
                <li class="nav-item mb-1">
                    <a class="nav-link sidebar-link {{ request()->routeIs('seller.dashboard') ? 'active' : '' }}"
                    href="{{ route('seller.dashboard') }}">
                        📊 Dashboard
                    </a>
                </li>

                {{-- PRODUK --}}
                <li class="nav-item mb-1">
                    <a class="nav-link sidebar-link {{ request()->routeIs('products*') ? 'active' : '' }}"
                    href="{{ route('seller.products.index') }}">
                        📦 Kelola Produk
                    </a>
                </li>

                {{-- RATING --}}
                <li class="nav-item mb-1">
                    <a class="nav-link sidebar-link {{ request()->routeIs('seller.ratings.index') ? 'active' : '' }}"
                    href="/seller/ratings">
                        ⭐ Kelola Rating
                    </a>
                </li>

                {{-- TRANSAKSI (DROPDOWN) --}}
                <li class="nav-item mb-1">

                    <a class="nav-link sidebar-link d-flex justify-content-between align-items-center"
                    data-bs-toggle="collapse"
                    href="#transaksiMenu"
                    role="button"
                    aria-expanded="false"
                    aria-controls="transaksiMenu">

                        💰 Transaksi
                        <span class="text-muted">▾</span>

                    </a>

                    <div class="collapse {{ request()->is('seller/orders*') || request()->is('seller/rentals*') ? 'show' : '' }}"
                        id="transaksiMenu">

                        <ul class="nav flex-column ms-3 mt-1">

                            <li class="nav-item">
                                <a class="nav-link sidebar-sub {{ request()->is('seller/orders*') ? 'active' : '' }}"
                                href="/seller/orders">
                                    🧾 Pesanan Baru
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link sidebar-sub {{ request()->is('seller/rentals*') ? 'active' : '' }}"
                                href="/seller/rentals">
                                    🏕️ Penyewaan Alat
                                </a>
                            </li>

                        </ul>

                    </div>
                </li>

                {{-- CHAT --}}
                <li class="nav-item mb-1">
                    <a class="nav-link sidebar-link {{ request()->routeIs('chat.index') ? 'active' : '' }}"
                    href="/seller/chat">
                        💬 Chat Pembeli
                    </a>
                </li>

            </ul>
        </div>

        {{-- BOTTOM --}}
        <div class="px-3 pb-4">
            <hr>
            <a class="nav-link sidebar-link {{ request()->routeIs('seller.store-profile*') ? 'bg-success text-white rounded px-3 py-2' : 'text-dark' }}" href="{{ route('seller.store-profile.index') }}"">
                👤 Profil Toko
            </a>
        </div>
    </div>

    {{-- MAIN CONTENT (UPGRADED UI + ORIGINAL KPI CARDS RESTORED) --}}
    <div class="flex-grow-1 p-4" style="background:#f9fafb; min-height:100vh;">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <h3 class="fw-bold mb-1">Customer Feedback</h3>
                <p class="text-muted mb-0">Monitor your store reputation and product quality</p>
            </div>
        </div>

        {{-- KPI CARDS (RESTORED LIKE YOUR ORIGINAL) --}}
        <div class="row g-3 mb-4">

            {{-- RATING SELLER --}}
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3" style="border-radius:16px;">
                    <small class="text-muted">Rating Seller</small>
                    <h4 class="fw-bold mt-1">{{ $averageRating }}/5.0</h4>
                    <small class="text-success">Gabungan toko & produk</small>
                </div>
            </div>

            {{-- RATING PRODUK --}}
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3" style="border-radius:16px;">
                    <small class="text-muted">Rating Produk</small>
                    <h4 class="fw-bold mt-1">{{ $avgProductRating }}/5.0</h4>
                    <small class="text-primary">{{ $totalProductReviews }} ulasan</small>
                </div>
            </div>

            {{-- RATING TOKO --}}
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3" style="border-radius:16px;">
                    <small class="text-muted">Rating Toko</small>
                    <h4 class="fw-bold mt-1">{{ $avgStoreRating }}/5.0</h4>
                    <small class="text-warning">{{ $totalStoreReviews }} ulasan</small>
                </div>
            </div>

            {{-- ULASAN BINTANG 5 --}}
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3" style="border-radius:16px;">
                    <small class="text-muted">Ulasan Bintang 5</small>
                    <h4 class="fw-bold mt-1">{{ $fiveStar }}</h4>
                    <small class="text-success">Review terbaik</small>
                </div>
            </div>

        </div>

        <div class="row g-4">

            {{-- LEFT --}}
            <div class="col-md-8">

                <h5 class="fw-bold mb-3">Recent Reviews</h5>

                {{-- PRODUCT REVIEWS --}}
                @foreach($productRatings as $rating)
                <div class="card border-0 shadow-sm p-3 mb-3" style="border-radius:14px;">

                    <div class="d-flex justify-content-between">
                        <div>
                            <strong>{{ $rating->user->name ?? 'User' }}</strong><br>
                            <small class="text-muted">{{ $rating->product->nama_produk ?? '-' }}</small>
                        </div>
                        <div class="text-warning">⭐ {{ $rating->rating }}/5</div>
                    </div>

                    <p class="mt-2 mb-0 text-muted small">{{ $rating->ulasan ?? '-' }}</p>

                    {{-- REPLY --}}
                    @if($rating->reply)
                    <div class="mt-3 p-2 bg-light rounded">
                        <small class="text-primary fw-bold">Balasan Seller:</small>
                        <p class="mb-0 small">{{ $rating->reply }}</p>
                    </div>
                    @else
                    <button class="btn btn-sm btn-outline-primary mt-2" data-bs-toggle="modal" data-bs-target="#replyModal" data-rating-id="{{ $rating->id }}" data-type="product">
                        Balas Ulasan
                    </button>
                    @endif
                </div>
                @endforeach

                {{-- STORE REVIEWS --}}
                @foreach($storeRatings as $rating)
                <div class="card border-0 shadow-sm p-3 mb-3" style="border-radius:14px;">

                    <div class="d-flex justify-content-between">
                        <div>
                            <strong>{{ $rating->user->name ?? 'User' }}</strong><br>
                            <small class="text-muted">Store Review</small>
                        </div>
                        <div class="text-warning">⭐ {{ $rating->rating }}/5</div>
                    </div>

                    <p class="mt-2 mb-0 text-muted small">{{ $rating->ulasan ?? '-' }}</p>

                    {{-- REPLY --}}
                    @if($rating->reply)
                    <div class="mt-3 p-2 bg-light rounded">
                        <small class="text-primary fw-bold">Balasan Seller:</small>
                        <p class="mb-0 small">{{ $rating->reply }}</p>
                    </div>
                    @else
                    <button class="btn btn-sm btn-outline-primary mt-2" data-bs-toggle="modal" data-bs-target="#replyModal" data-rating-id="{{ $rating->id }}" data-type="store">
                        Balas Ulasan
                    </button>
                    @endif
                </div>
                @endforeach

            </div>

            {{-- RIGHT --}}
            <div class="col-md-4">

                <div class="card border-0 shadow-sm p-3 mb-3" style="border-radius:16px;">
                    <h6 class="fw-bold">Rating Distribution</h6>

                    <p class="mb-1">⭐⭐⭐⭐⭐ {{ $fiveStar }}</p>
                    <p class="mb-1">⭐⭐⭐⭐ {{ $fourStar }}</p>
                    <p class="mb-1">⭐⭐⭐ {{ $threeStar }}</p>
                    <p class="mb-1">⭐⭐ {{ $twoStar }}</p>
                    <p class="mb-0">⭐ {{ $oneStar }}</p>
                </div>

                <div class="card border-0 text-white p-3" style="border-radius:16px; background:linear-gradient(135deg,#10B981,#065F46);">
                    <h6 class="fw-bold">Improve Your Rating</h6>
                    <p class="small">Respond quickly and maintain product quality.</p>
                    <button class="btn btn-light btn-sm rounded-pill">Learn Tips</button>
                </div>

            </div>

        </div>

    </div>
@endsection

{{-- REPLY MODAL --}}
<div class="modal fade" id="replyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Balas Ulasan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="replyForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="replyText" class="form-label">Balasan Anda</label>
                        <textarea class="form-control" id="replyText" name="reply" rows="4" placeholder="Tulis balasan untuk ulasan ini..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Kirim Balasan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const replyModal = document.getElementById('replyModal');
    const replyForm = document.getElementById('replyForm');

    replyModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const ratingId = button.getAttribute('data-rating-id');
        const type = button.getAttribute('data-type');

        // Set action URL
        const actionUrl = type === 'product'
            ? `/seller/ratings/product/${ratingId}/reply`
            : `/seller/ratings/store/${ratingId}/reply`;

        replyForm.action = actionUrl;
    });

    // Reset form when modal is hidden
    replyModal.addEventListener('hidden.bs.modal', function() {
        replyForm.reset();
    });
});
</script>

