@php
use Illuminate\Support\Facades\Auth;
use App\Models\SellerModels\Product_seller;
use App\Models\Conversation;

$userId = Auth::id();

/* =========================
   PRODUK SELLER
========================= */
$products = Product_seller::where('user_id', $userId)->get();

/* =========================
   SELECTED PRODUCT
========================= */
$selectedProductId = request('product');
$selectedProduct = $selectedProductId
    ? $products->where('id', $selectedProductId)->first()
    : $products->first();

/* =========================
   CONVERSATIONS (BERDASARKAN PRODUK)
========================= */
$conversations = collect();

if ($selectedProduct) {
    $conversations = Conversation::with(['buyer', 'latestMessage'])
        ->where('product_id', $selectedProduct->id)
        ->where('seller_id', $userId)
        ->latest()
        ->get();
}

/* =========================
   COUNT CHAT PER PRODUK
========================= */
$productChatCount = Conversation::where('seller_id', $userId)
    ->selectRaw('product_id, count(*) as total')
    ->groupBy('product_id')
    ->pluck('total', 'product_id');
@endphp

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

    {{-- MAIN --}}
    <div class="flex-grow-1 p-4">

        <h4 class="fw-bold mb-1">Diskusi Produk</h4>
        <small class="text-muted">Kelola pertanyaan pelanggan</small>

        <div class="row mt-4 g-3">

            {{-- =========================
                 KIRI: PRODUK
            ========================= --}}
            <div class="col-md-4">
                <div class="card border-0 shadow-sm" style="border-radius:16px;">
                    <div class="card-body">

                        <h6 class="fw-bold mb-3">Daftar Produk Aktif</h6>

                        @foreach($products as $p)
                            <a href="?product={{ $p->id }}" class="text-decoration-none">
                                <div class="p-3 mb-2 rounded"
                                     style="background: {{ $selectedProduct?->id == $p->id ? '#eafff3' : '#f8f9fa' }};
                                            border:1px solid #eee;">

                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <strong class="text-dark">{{ $p->nama_produk }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                {{ $productChatCount[$p->id] ?? 0 }} percakapan
                                            </small>
                                        </div>

                                        @if(($productChatCount[$p->id] ?? 0) > 0)
                                            <span class="badge bg-success">
                                                {{ $productChatCount[$p->id] }}
                                            </span>
                                        @endif
                                    </div>

                                </div>
                            </a>
                        @endforeach

                    </div>
                </div>
            </div>

            {{-- =========================
                 KANAN: CHAT
            ========================= --}}
            <div class="col-md-8">

                <div class="card border-0 shadow-sm" style="border-radius:16px;">
                    <div class="card-body">

                        @if($selectedProduct)
                            <h6 class="fw-bold mb-3">
                                {{ $selectedProduct->nama_produk }}
                            </h6>
                        @endif

                        @forelse($conversations as $conv)
                            <a href="{{ route('seller.chat.show', $conv->id) }}" class="text-decoration-none text-dark">
                                <div class="d-flex align-items-start p-3 mb-2 rounded"
                                    style="background:#f8f9fa; cursor:pointer;">

                                {{-- USER ICON --}}
                                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                     style="width:45px;height:45px;">
                                    {{ strtoupper(substr($conv->buyer->name ?? 'U',0,1)) }}
                                </div>

                                {{-- CHAT --}}
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between">
                                        <strong>{{ $conv->buyer->name ?? 'User' }}</strong>
                                        <small class="text-muted">
                                            {{ $conv->latestMessage?->created_at?->format('H:i') }}
                                        </small>
                                    </div>

                                    <small class="text-muted">
                                        {{ \Illuminate\Support\Str::limit($conv->latestMessage?->message, 60) }}
                                    </small>
                                </div>

                            </div>
                        @empty
                            <div class="text-center text-muted p-5">
                                Belum ada chat untuk produk ini
                            </div>
                        @endforelse

                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
@endsection