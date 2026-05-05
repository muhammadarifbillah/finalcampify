@extends('SellerView.layouts.app_seller')

@section('content')
<div class="d-flex" style="min-height:100vh; background:#f9fafb;">
    <div style="width:260px; background:white; border-right:1px solid #eee; display:flex; flex-direction:column; justify-content:space-between;">
        <div>
            <div class="p-4">
                <h4 style="color:#10B981; font-weight:800;">CAMPIFY.</h4>
                <small class="text-muted">SELLER HUB</small>
            </div>

            <ul class="nav flex-column px-3">
                <li class="nav-item mb-2"><a class="nav-link {{ request()->routeIs('seller.dashboard') ? 'bg-success text-white rounded px-3 py-2' : 'text-dark' }}" href="{{ route('seller.dashboard') }}">Dashboard</a></li>
                <li class="nav-item mb-2"><a class="nav-link {{ request()->routeIs('seller.products*') ? 'bg-success text-white rounded px-3 py-2' : 'text-dark' }}" href="{{ route('seller.products.index') }}">Kelola Produk</a></li>
                <li class="nav-item mb-2"><a class="nav-link {{ request()->routeIs('seller.ratings.index') ? 'bg-success text-white rounded px-3 py-2' : 'text-dark' }}" href="{{ route('seller.ratings.index') }}">Kelola Rating</a></li>
                <li class="nav-item mb-2"><a class="nav-link {{ request()->routeIs('seller.orders*') ? 'bg-success text-white rounded px-3 py-2' : 'text-dark' }}" href="{{ route('seller.orders.index') }}">Pesanan Baru</a></li>
                <li class="nav-item mb-2"><a class="nav-link {{ request()->routeIs('seller.rentals*') ? 'bg-success text-white rounded px-3 py-2' : 'text-dark' }}" href="{{ route('seller.rentals.index') }}">Penyewaan Alat</a></li>
                <li class="nav-item mb-2"><a class="nav-link {{ request()->routeIs('seller.chat*') ? 'bg-success text-white rounded px-3 py-2' : 'text-dark' }}" href="{{ route('seller.chat.index') }}">Chat Pembeli</a></li>
            </ul>
        </div>

        <div class="px-3 pb-4">
            <hr>
            <a class="nav-link {{ request()->routeIs('seller.store-profile*') ? 'bg-success text-white rounded px-3 py-2' : 'text-dark' }}" href="{{ route('seller.store-profile.index') }}">Profil Toko</a>
        </div>
    </div>

    <div class="flex-grow-1 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">CHAT PEMBELI</h4>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius:16px;">
            <div class="card-body p-2">
                @forelse($conversations as $legacyUserId => $conversation)
                    @php
                        $isConversation = $conversation instanceof \App\Models\Conversation;
                        $chats = $isConversation ? collect() : collect($conversation);
                        $lastChat = $isConversation ? $conversation->latestMessage : $chats->first();
                        $partner = $isConversation
                            ? $conversation->buyer
                            : (($lastChat?->sender_id == \Illuminate\Support\Facades\Auth::id()) ? $lastChat?->receiver : $lastChat?->sender);
                        $showUrl = $isConversation
                            ? route('seller.chat.show', $conversation)
                            : route('seller.chat.legacy.show', ['userId' => $legacyUserId]);
                        $productName = $isConversation ? ($conversation->product?->name ?? 'Produk') : 'Chat lama';
                        $unread = $isConversation
                            ? $conversation->messages()->where('sender_id', '!=', \Illuminate\Support\Facades\Auth::id())->whereNull('read_at')->count()
                            : $chats->where('receiver_id', \Illuminate\Support\Facades\Auth::id())->where('is_read', false)->count();
                    @endphp

                    <a href="{{ $showUrl }}" class="text-decoration-none">
                        <div class="d-flex align-items-center p-3 mb-2 rounded chat-item">
                            <div class="bg-success rounded-circle text-white d-flex align-items-center justify-content-center me-3" style="width:50px;height:50px;">
                                {{ strtoupper(substr($partner->name ?? 'U', 0, 1)) }}
                            </div>

                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <h6 class="mb-0 text-dark">{{ $partner->name ?? 'User' }}</h6>
                                    <small class="text-muted">{{ $lastChat?->created_at?->format('H:i') }}</small>
                                </div>
                                <p class="text-muted small mb-0">
                                    <span class="text-success">{{ $productName }}</span>
                                    - {{ Str::limit($lastChat?->message ?? 'Belum ada pesan', 50) }}
                                </p>
                            </div>

                            @if($unread > 0)
                                <span class="badge bg-success rounded-pill ms-2">{{ $unread }}</span>
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

    <style>
        .chat-item { background: #f8f9fa; transition: 0.2s ease; }
        .chat-item:hover { background: #eef2f7; transform: scale(1.01); }
    </style>
</div>
@endsection
