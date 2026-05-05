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
            <div class="d-flex align-items-center">
                <a href="/seller/chat" class="btn btn-light rounded-pill px-3 me-3">←</a>
                <h4 class="fw-bold mb-0">{{ $chatPartner->name ?? 'Chat' }}</h4>
            </div>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius:16px; height: 60vh; display: flex; flex-direction: column;">
            <div class="card-body flex-grow-1 overflow-auto">
                @forelse($messages as $message)
                    <div class="mb-3 {{ $message->sender_id == auth()->id() ? 'text-end' : '' }}">
                        <div class="d-inline-block p-3 rounded {{ $message->sender_id == auth()->id() ? 'bg-success text-white' : 'bg-light' }}" style="max-width: 70%;">
                            {{ $message->message }}
                        </div>
                        <br>
                        <small class="text-muted">{{ $message->created_at->format('H:i') }}</small>
                    </div>
                @empty
                    <p class="text-center text-muted">Belum ada pesan</p>
                @endforelse
            </div>

            <div class="card-footer border-0">
                <form method="POST" action="/seller/chat" class="d-flex">
                    @csrf
                    <input type="hidden" name="receiver_id" value="{{ $chatPartner->id }}">
                    <input type="text" name="message" class="form-control me-2" placeholder="Ketik pesan..." required>
                    <button type="submit" class="btn text-white" style="background:#10B981;">Kirim</button>
                </form>
            </div>
        </div>

    </div>

</div>
@endsection