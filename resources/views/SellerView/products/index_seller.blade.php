@extends('SellerView.layouts.app_seller')

@section('content')

<div class="d-flex" style="min-height: 100vh; background:#f9fafb;">

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

    {{-- CONTENT --}}
    <div class="flex-grow-1 p-4">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">PRODUCTS</h4>
            <div>
                <a href="{{ route('seller.products.create') }}" class="btn btn-success rounded-pill px-4">
                    + Tambah Produk
                </a>
            </div>
        </div>

        {{-- FILTER --}}
        <div class="mb-4">
            <a href="{{ route('seller.products.index') }}" 
            class="btn btn-sm {{ request('jenis') == null ? 'btn-success' : 'btn-outline-secondary' }}">
                SEMUA
            </a>

            <a href="{{ route('seller.products.index', ['jenis' => 'sewa']) }}" 
            class="btn btn-sm {{ request('jenis') == 'sewa' ? 'btn-success' : 'btn-outline-secondary' }}">
                SEWA
            </a>

            <a href="{{ route('seller.products.index', ['jenis' => 'jual']) }}" 
            class="btn btn-sm {{ request('jenis') == 'jual' ? 'btn-success' : 'btn-outline-secondary' }}">
                JUAL
            </a>
        </div>

        {{-- ALERT --}}
        @if(session('success'))
            <div class="alert alert-success rounded-3">
                {{ session('success') }}
            </div>
        @endif

        {{-- PRODUK GRID --}}
        <div class="row g-4">

            @forelse ($products as $product)
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100" style="border-radius:16px; overflow:hidden;">

                    {{-- IMAGE --}}
                    <div style="height:220px; background:#f3f4f6; display:flex; align-items:center; justify-content:center;">

                        @if($product->gambar && file_exists(public_path('storage/'.$product->gambar)))
                            <img src="{{ asset('storage/'.$product->gambar) }}"
                                 alt="{{ $product->nama_produk }}"
                                 style="width:100%; height:100%; object-fit:cover;">
                        @elseif($product->gambar)
                            <img src="{{ asset('storage/'.$product->gambar) }}"
                                 alt="{{ $product->nama_produk }}"
                                 style="width:100%; height:100%; object-fit:cover;">
                        @else
                            <div class="text-center">
                                <span style="font-size:40px;">🏕️</span>
                                <p class="text-muted mt-2 mb-0">No Image</p>
                            </div>
                        @endif

                    </div>

                    <div class="card-body d-flex flex-column">

                        {{-- BADGE JENIS PRODUK --}}
                        @if($product->jenis_produk == 'sewa')
                            <span class="badge bg-primary mb-2">SEWA</span>
                        @else
                            <span class="badge bg-success mb-2">JUAL</span>
                        @endif

                        {{-- KATEGORI --}}
                        <p class="small mb-1 fw-semibold" style="color:#10B981;">
                            {{ ucfirst(str_replace('_',' ',$product->kategori ?? 'Tanpa Kategori')) }}
                        </p>

                        {{-- NAMA --}}
                        <h6 class="fw-bold mb-2">
                            {{ $product->nama_produk }}
                        </h6>

                        {{-- DESKRIPSI --}}
                        <p class="text-muted small mb-3">
                            {{ Str::limit($product->deskripsi, 55) }}
                        </p>

                        {{-- HARGA --}}
                        <h5 class="fw-bold mb-2">
                            Rp {{ number_format($product->harga,0,',','.') }}

                            @if($product->jenis_produk == 'sewa')
                                <small class="text-muted fs-6">/hari</small>
                            @endif
                        </h5>

                        {{-- RATING --}}
                        @php
                            $avgRating = $product->averageRating();
                            $ratingCount = $product->ratingCount();
                        @endphp
                        <div class="mb-3">
                            <div class="d-flex align-items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= round($avgRating))
                                        <span style="color:#F59E0B;">★</span>
                                    @else
                                        <span style="color:#D1D5DB;">★</span>
                                    @endif
                                @endfor
                                <span class="small text-muted ms-2">
                                    {{ number_format($avgRating, 1) }} ({{ $ratingCount }} ulasan)
                                </span>
                            </div>
                        </div>

                        {{-- FOOTER --}}
                        <div class="d-flex justify-content-between mt-auto small text-muted border-top pt-3">
                            <span>
                                STOK:
                                <strong class="{{ $product->stok > 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $product->stok ?? 0 }}
                                </strong>
                            </span>

                            <span>ID #{{ $product->id }}</span>
                        </div>

                        {{-- ACTION --}}
                        <div class="mt-3 d-flex gap-2">
                            <a href="/seller/products/{{ $product->id }}/edit"
                               class="btn btn-warning btn-sm w-50">
                               Edit
                            </a>

                            <form action="{{ route('seller.products.destroy', $product->id) }}" method="POST">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Yakin ingin menghapus produk ini?')">
                                    Hapus
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>

            @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm p-5 text-center" style="border-radius:16px;">
                    <div style="font-size:60px;">📦</div>
                    <h5 class="fw-bold mt-3">Belum Ada Produk</h5>
                    <p class="text-muted">Tambahkan produk jual atau sewa pertama kamu sekarang.</p>

                    <div class="mt-3">
                        <a href="{{ route('seller.products.create') }}" class="btn btn-success rounded-pill px-4">
                            + Tambah Produk
                        </a>
                    </div>
                </div>
            </div>
            @endforelse

        </div>

    </div>

</div>

@endsection

