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
                    href="{{ route('products') }}">
                    Kelola Produk
                    </a>
                </li>

                    <li class="nav-item mb-2">
                        <a class="nav-link {{ request()->routeIs('ratings.index') ? 'bg-success text-white rounded px-3 py-2' : 'text-dark' }}"
                        href="/ratings">
                        Kelola Rating
                        </a>
                    </li>

                <li class="nav-item mb-2">
                    <a class="nav-link {{ request()->routeIs('orders*') ? 'bg-success text-white rounded px-3 py-2' : 'text-dark' }}"
                    href="/orders">
                    Pesanan Baru
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a class="nav-link {{ request()->routeIs('rentals.index') ? 'bg-success text-white rounded px-3 py-2' : 'text-dark' }}"
                    href="/rentals">
                    Penyewaan Alat
                    </a>
                </li>

                {{-- CHAT TETAP DI ATAS --}}
                <li class="nav-item mb-2">
                    <a class="nav-link {{ request()->routeIs('chat.index') ? 'bg-success text-white rounded px-3 py-2' : 'text-dark' }}"
                    href="/chat">
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
                    href="/store-profile">
                        Profil Toko
                    </a>
                </li>
            </ul>
        </div>

    </div>

    {{-- CONTENT --}}
    <div class="flex-grow-1 p-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">DETAIL PRODUK</h4>
            <a href="/products" class="btn btn-outline-secondary rounded-pill">Kembali</a>
        </div>

        <div class="row g-4">
            {{-- GAMBAR PRODUK --}}
            <div class="col-md-5">
                <div class="card border-0 shadow-sm" style="border-radius:16px; overflow:hidden;">
                    <div style="height:350px; background:#f3f4f6; display:flex; align-items:center; justify-content:center;">
                        @if($product->gambar && file_exists(public_path('storage/'.$product->gambar)))
                            <img src="{{ asset('storage/'.$product->gambar) }}" 
                                 alt="{{ $product->nama_produk }}" 
                                 style="width:100%; height:100%; object-fit:cover;">
                        @else
                            <div class="text-center">
                                <span style="font-size:80px;">🏕️</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- INFO PRODUK --}}
            <div class="col-md-7">
                <div class="card border-0 shadow-sm p-4" style="border-radius:16px;">
                    
                    @if($product->jenis_produk == 'sewa')
                        <span class="badge bg-primary mb-2">SEWA</span>
                    @else
                        <span class="badge bg-success mb-2">JUAL</span>
                    @endif

                    <span class="badge bg-secondary mb-2">{{ ucfirst($product->kategori) }}</span>

                    <h3 class="fw-bold mt-2">{{ $product->nama_produk }}</h3>

                    {{-- RATING PRODUK --}}
                    <div class="d-flex align-items-center mb-3">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= round($avgProductRating))
                                <span style="color:#F59E0B; font-size:20px;">★</span>
                            @else
                                <span style="color:#D1D5DB; font-size:20px;">★</span>
                            @endif
                        @endfor
                        <span class="ms-2 text-muted">{{ number_format($avgProductRating, 1) }} ({{ $productRatingCount }} ulasan)</span>
                    </div>

                    <h4 class="fw-bold text-success mb-3">
                        Rp {{ number_format($product->harga,0,',','.') }}
                        @if($product->jenis_produk == 'sewa')
                            <small class="text-muted">/hari</small>
                        @endif
                    </h4>

                    <p class="text-muted">{{ $product->deskripsi }}</p>

                    <div class="d-flex gap-3 mt-4">
                        <div class="p-3 bg-light rounded-3">
                            <small class="text-muted d-block">STOK</small>
                            <strong>{{ $product->stok }}</strong>
                        </div>
                        <div class="p-3 bg-light rounded-3">
                            <small class="text-muted d-block">TERJUAL</small>
                            <strong>{{ $product->orders->count() ?? 0 }}</strong>
                        </div>
                    </div>

                    {{-- TOMBOL --}}
                    <div class="mt-4">
                        @auth
                            @if($product->jenis_produk == 'jual')
                                <button class="btn btn-success rounded-pill px-4">Beli Sekarang</button>
                            @else
                                <button class="btn btn-primary rounded-pill px-4">Sewa Sekarang</button>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn btn-success rounded-pill px-4">Login untuk Beli/Sewa</a>
                        @endauth
                    </div>

                </div>
            </div>
        </div>

        {{-- RATING & ULASAN PRODUK --}}
        <div class="card border-0 shadow-sm p-4 mt-4" style="border-radius:16px;">
            <h6 class="fw-bold mb-3">ULASAN PRODUK</h6>
            
            @forelse($productRatings as $pr)
            <div class="border-bottom py-3">
                <div class="d-flex justify-content-between">
                    <strong>{{ $pr->user->name ?? 'User' }}</strong>
                    <small class="text-muted">{{ $pr->created_at->diffForHumans() }}</small>
                </div>
                <div class="mb-1">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= $pr->rating)
                            <span style="color:#F59E0B;">★</span>
                        @else
                            <span style="color:#D1D5DB;">★</span>
                        @endif
                    @endfor
                </div>
                <p class="mb-0 text-muted">{{ $pr->ulasan ?? '-' }}</p>
            </div>
            @empty
            <p class="text-muted">Belum ada ulasan untuk produk ini.</p>
            @endforelse
        </div>

        {{-- RATING TOKO --}}
        <div class="card border-0 shadow-sm p-4 mt-4" style="border-radius:16px;">
            <h6 class="fw-bold mb-3">RATING TOKO</h6>
            
            <div class="d-flex align-items-center mb-3">
                <h3 class="fw-bold me-2">{{ number_format($avgStoreRating, 1) }}</h3>
                <div>
                    <div>
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= round($avgStoreRating))
                                <span style="color:#F59E0B; font-size:20px;">★</span>
                            @else
                                <span style="color:#D1D5DB; font-size:20px;">★</span>
                            @endif
                        @endfor
                    </div>
                    <small class="text-muted">{{ $storeRatingCount }} ulasan</small>
                </div>
            </div>

            @forelse($storeRatings as $sr)
            <div class="border-bottom py-3">
                <div class="d-flex justify-content-between">
                    <strong>{{ $sr->user->name ?? 'User' }}</strong>
                    <small class="text-muted">{{ $sr->created_at->diffForHumans() }}</small>
                </div>
                <div class="mb-1">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= $sr->rating)
                            <span style="color:#F59E0B;">★</span>
                        @else
                            <span style="color:#D1D5DB;">★</span>
                        @endif
                    @endfor
                </div>
                <p class="mb-0 text-muted">{{ $sr->ulasan ?? '-' }}</p>
            </div>
            @empty
            <p class="text-muted">Belum ada rating untuk toko ini.</p>
            @endforelse
        </div>

        {{-- FORM BERI RATING --}}
        @if(Auth::check() && Auth::id() !== $product->user_id)
        <div class="card border-0 shadow-sm p-4 mt-4" style="border-radius:16px;">
            <h6 class="fw-bold mb-3">BERI RATING</h6>
            
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            
            <form method="POST" action="{{ route('ratings.product') }}">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Rating Produk</label>
                    <div class="d-flex gap-2">
                        @for($i = 1; $i <= 5; $i++)
                            <input type="radio" class="btn-check" name="rating" id="product_rating_{{ $i }}" value="{{ $i }}" 
                                {{ old('rating') == $i ? 'checked' : '' }}>
                            <label class="btn btn-outline-warning" for="product_rating_{{ $i }}">{{ $i }} ★</label>
                        @endfor
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Ulasan</label>
                    <textarea name="ulasan" class="form-control" rows="2" placeholder="Tulis ulasan Anda...">{{ old('ulasan') }}</textarea>
                </div>
                
                <button type="submit" class="btn btn-success rounded-pill px-4">Kirim Rating</button>
            </form>
            
            <hr class="my-4">
            
            <form method="POST" action="{{ route('ratings.store') }}">
                @csrf
                <input type="hidden" name="store_id" value="{{ $product->user_id }}">
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Rating Toko</label>
                    <div class="d-flex gap-2">
                        @for($i = 1; $i <= 5; $i++)
                            <input type="radio" class="btn-check" name="rating" id="store_rating_{{ $i }}" value="{{ $i }}" 
                                {{ old('rating') == $i ? 'checked' : '' }}>
                            <label class="btn btn-outline-warning" for="store_rating_{{ $i }}">{{ $i }} ★</label>
                        @endfor
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Ulasan Toko</label>
                    <textarea name="ulasan" class="form-control" rows="2" placeholder="Tulis ulasan untuk toko...">{{ old('ulasan') }}</textarea>
                </div>
                
                <button type="submit" class="btn btn-primary rounded-pill px-4">Kirim Rating Toko</button>
            </form>
        </div>
        @elseif(!Auth::check())
        <div class="card border-0 shadow-sm p-4 mt-4 text-center" style="border-radius:16px;">
            <p class="text-muted mb-0">Silakan <a href="{{ route('login') }}">login</a> untuk memberikan rating.</p>
        </div>
        @endif

    </div>
</div>
@endsection