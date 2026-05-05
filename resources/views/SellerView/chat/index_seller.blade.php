@extends('SellerView.layouts.app_seller')

@section('content')
<div class="d-flex" style="min-height:100vh; background:#f9fafb;">

    {{-- SIDEBAR --}}
    <div style="width:260px; background:white; border-right:1px solid #eee; display:flex; flex-direction:column; justify-content:space-between;">
        
        {{-- TOP --}}
        <div>
            <div class="p-4">
                <h4 style="color:#10B981; font-weight:800;">CAMPIFY.</h4>
                <small class="text-muted">SELLER HUB</small>
            </div>

            <ul class="nav flex-column px-3">

                <li class="nav-item mb-2">
                    <a class="nav-link {{ request()->routeIs('seller.dashboard') ? 'bg-success text-white rounded px-3 py-2' : 'text-dark' }}"
                    href="{{ route('seller.dashboard') }}">
                    Dashboard
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a class="nav-link {{ request()->routeIs('products*') ? 'bg-success text-white rounded px-3 py-2' : 'text-dark' }}"
                    href="{{ route('products.index') }}">
                    Kelola Produk
                    </a>
                </li>

                    <li class="nav-item mb-2">
                        <a class="nav-link {{ request()->routeIs('ratings.index') ? 'bg-success text-white rounded px-3 py-2' : 'text-dark' }}"
                        href="/seller/ratings">
                        Kelola Rating
                        </a>
                    </li>

                <li class="nav-item mb-2">
                    <a class="nav-link {{ request()->routeIs('orders*') ? 'bg-success text-white rounded px-3 py-2' : 'text-dark' }}"
                    href="/seller/orders">
                    Pesanan Baru
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a class="nav-link {{ request()->routeIs('rentals*') ? 'bg-success text-white rounded px-3 py-2' : 'text-dark' }}"
                    href="/seller/rentals">
                    Penyewaan Alat
                    </a>
                </li>

                {{-- CHAT TETAP DI ATAS --}}
                <li class="nav-item mb-2">
                    <a class="nav-link {{ request()->routeIs('chat*') ? 'bg-success text-white rounded px-3 py-2' : 'text-dark' }}"
                    href="/seller/chat">
                    Chat Pembeli
                    </a>
                </li>

            </ul>
        </div>

        {{-- BOTTOM --}}
        <div class="px-3 pb-4">
            <hr>

            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('store-profile*') ? 'bg-success text-white rounded px-3 py-2' : 'text-dark' }}"
                    href="/seller/store-profile">
                        Profil Toko
                    </a>
                </li>
            </ul>
        </div>

    </div>

    {{-- CONTENT --}}
<div class="flex-grow-1 p-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold">CHAT PEMBELI</h4>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius:16px;">
        
        {{-- container padding kecil biar item tidak nempel --}}
        <div class="card-body p-2">

            @forelse($conversations as $userId => $chats)
                @php
                    $lastChat = $chats->first();
                    $partner = $lastChat->sender_id == auth()->id()
                        ? $lastChat->receiver
                        : $lastChat->sender;

                    $unread = $chats
                        ->where('receiver_id', auth()->id())
                        ->where('is_read', false)
                        ->count();
                @endphp

                <a href="/seller/chat/{{ $partner->id }}" class="text-decoration-none">

                    <div class="d-flex align-items-center p-3 mb-2 rounded chat-item">

                        {{-- Avatar --}}
                        <div class="bg-success rounded-circle text-white d-flex align-items-center justify-content-center me-3"
                             style="width:50px;height:50px;">
                            {{ strtoupper(substr($partner->name ?? 'U', 0, 1)) }}
                        </div>

                        {{-- Content --}}
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <h6 class="mb-0 text-dark">
                                    {{ $partner->name ?? 'User' }}
                                </h6>

                                <small class="text-muted">
                                    {{ $lastChat->created_at->format('H:i') }}
                                </small>
                            </div>

                            <p class="text-muted small mb-0">
                                {{ Str::limit($lastChat->message, 50) }}
                            </p>
                        </div>

                        {{-- Badge unread --}}
                        @if($unread > 0)
                            <span class="badge bg-success rounded-pill ms-2">
                                {{ $unread }}
                            </span>
                        @endif

                    </div>
                </a>

            @empty
                <div class="p-4 text-center text-muted">
                    <p>Belum ada percakapan</p>
                </div>
            @endforelse

        </div>
    </div>
</div>

{{-- STYLE TAMBAHAN --}}
<style>
.chat-item {
    background: #f8f9fa;
    transition: 0.2s ease;
}

.chat-item:hover {
    background: #eef2f7;
    transform: scale(1.01);
}
</style>

</div>
@endsection