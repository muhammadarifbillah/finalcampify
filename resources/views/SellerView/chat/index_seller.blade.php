@php
use Illuminate\Support\Facades\Auth;
use App\Models\SellerModels\Product_seller;
use App\Models\Conversation;

$userId = Auth::id();
$products = Product_seller::where('user_id', $userId)->get();
$selectedProductId = request('product');
$selectedProduct = $selectedProductId ? $products->where('id', $selectedProductId)->first() : $products->first();

$conversations = collect();
if ($selectedProduct) {
    $conversations = Conversation::with(['buyer', 'latestMessage'])
        ->where('product_id', $selectedProduct->id)
        ->where('seller_id', $userId)
        ->latest()
        ->get();
}

$productChatCount = Conversation::where('seller_id', $userId)
    ->selectRaw('product_id, count(*) as total')
    ->groupBy('product_id')
    ->pluck('total', 'product_id');
@endphp

@extends('SellerView.layouts.app_seller')

@section('content')
<div class="dashboard-header mb-5">
    <h2 class="fw-bold m-0 text-dark">Diskusi Produk</h2>
    <p class="text-muted">Kelola pertanyaan dan diskusi pelanggan tentang barang Anda.</p>
</div>

<div class="row g-4">
    {{-- LEFT: PRODUCT LIST --}}
    <div class="col-lg-4">
        <div class="card card-modern border-0 h-100">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-4 small text-muted text-uppercase ls-1">Daftar Produk Aktif</h6>
                
                <div class="product-scroll-area" style="max-height: 600px; overflow-y: auto;">
                    @foreach($products as $p)
                        <a href="?product={{ $p->id }}" class="text-decoration-none d-block mb-3">
                            <div class="p-3 rounded-4 transition-all {{ $selectedProduct?->id == $p->id ? 'bg-emerald-soft border-emerald shadow-sm' : 'bg-light border-0 hover-light' }}"
                                 style="border: 1px solid transparent;">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-white rounded-3 shadow-sm d-flex align-items-center justify-content-center fw-bold text-emerald" style="width: 45px; height: 45px;">
                                        📦
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <h6 class="fw-bold m-0 text-dark text-truncate">{{ $p->nama_produk }}</h6>
                                        <small class="text-muted">{{ $productChatCount[$p->id] ?? 0 }} Percakapan</small>
                                    </div>
                                    @if(($productChatCount[$p->id] ?? 0) > 0)
                                        <span class="badge rounded-pill bg-emerald text-white px-2" style="background: var(--primary-emerald);">
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
    </div>

    {{-- RIGHT: CONVERSATION LIST --}}
    <div class="col-lg-8">
        <div class="card card-modern border-0 h-100">
            <div class="card-header bg-white p-4 border-bottom">
                @if($selectedProduct)
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-2 bg-emerald-soft rounded-3">📦</div>
                        <div>
                            <h6 class="fw-bold m-0">{{ $selectedProduct->nama_produk }}</h6>
                            <small class="text-muted">Chat pelanggan untuk produk ini</small>
                        </div>
                    </div>
                @else
                    <h6 class="fw-bold m-0">Pilih Produk</h6>
                @endif
            </div>
            
            <div class="card-body p-4">
                @forelse($conversations as $conv)
                    <a href="{{ route('seller.chat.show', $conv->id) }}" class="text-decoration-none d-block mb-3">
                        <div class="d-flex align-items-center gap-4 p-4 rounded-4 bg-light hover-shadow transition-all border border-transparent">
                            {{-- BUYER AVATAR --}}
                            <div class="avatar bg-white text-emerald rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm"
                                 style="width: 55px; height: 55px; font-size: 1.2rem;">
                                {{ strtoupper(substr($conv->buyer->name ?? 'U', 0, 1)) }}
                            </div>

                            {{-- MESSAGE CONTENT --}}
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="fw-bold m-0 text-dark">{{ $conv->buyer->name ?? 'User' }}</h6>
                                    <small class="text-muted">{{ $conv->latestMessage?->created_at?->diffForHumans() }}</small>
                                </div>
                                <p class="text-muted small m-0 text-truncate">
                                    {{ $conv->latestMessage?->message ?: 'Belum ada pesan.' }}
                                </p>
                            </div>

                            {{-- ACTION ICON --}}
                            <div class="text-emerald opacity-50">
                                <i class="bi bi-chevron-right fs-4"></i>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="text-center py-5">
                        <div class="mb-4 fs-1 opacity-10">💬</div>
                        <h5 class="fw-bold text-muted">Belum Ada Chat</h5>
                        <p class="text-muted small">Percakapan dengan pembeli akan muncul di sini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<style>
    .ls-1 { letter-spacing: 1px; }
    .transition-all { transition: all 0.2s ease; }
    .hover-light:hover { background-color: #f1f5f9 !important; }
    .hover-shadow:hover { transform: translateY(-3px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); border-color: var(--primary-emerald); }
    .bg-emerald-soft { background-color: var(--soft-emerald); }
    .border-emerald { border: 1px solid var(--primary-emerald) !important; }
    .product-scroll-area::-webkit-scrollbar { width: 5px; }
    .product-scroll-area::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>
@endsection