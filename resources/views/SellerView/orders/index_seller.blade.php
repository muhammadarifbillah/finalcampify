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

    {{-- CONTENT --}}
    <div class="flex-grow-1 p-4">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">KELOLA PESANAN</h4>
            
            {{-- FILTER --}}
            
        </div>

        @if(session('success'))
            <div class="alert alert-success rounded-3 mb-4">
                {{ session('success') }}
            </div>
        @endif

        {{-- TABLE CARD --}}
        <div class="card border-0 shadow-sm" style="border-radius:16px; overflow:hidden;">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background:#f9fafb;">
                        <tr>
                            <th class="ps-4">Produk</th>
                            <th>Pembeli</th>
                            <th>Status</th>
                            <th>Resi</th>
                            <th>Total</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div style="width:60px; height:60px; background:#f3f4f6; border-radius:12px; overflow:hidden; flex-shrink:0;">
                                        @if($order->product && $order->product->gambar && file_exists(public_path('storage/'.$order->product->gambar)))
                                            <img src="{{ asset('storage/'.$order->product->gambar) }}" style="width:100%; height:100%; object-fit:cover;">
                                        @else
                                            <div class="d-flex align-items-center justify-content-center h-100">
                                                <span style="font-size:24px;">🏕️</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ms-3">
                                        <p class="mb-0 fw-bold">{{ $order->product->nama_produk ?? '-' }}</p>
                                        <small class="text-muted">Qty: {{ $order->qty ?? 1 }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <p class="mb-0 fw-semibold">{{ $order->buyer->name ?? '-' }}</p>
                                <small class="text-muted">{{ $order->buyer->phone ?? '-' }}</small>
                            </td>
                            <td>
                                @if($order->status == 'pending')
                                    <span class="badge bg-warning text-dark rounded-pill px-3">Pending</span>
                                @elseif($order->status == 'processing' || $order->status == 'diproses')
                                    <span class="badge bg-primary rounded-pill px-3">Diproses</span>
                                @elseif($order->status == 'shipped' || $order->status == 'dikirim')
                                    <span class="badge bg-info text-dark rounded-pill px-3">Dikirim</span>
                                @elseif($order->status == 'completed' || $order->status == 'selesai')
                                    <span class="badge bg-success rounded-pill px-3">Selesai</span>
                                @else
                                    <span class="badge bg-secondary rounded-pill px-3">{{ $order->status }}</span>
                                @endif
                            </td>
                            <td>
                                @if($order->resi)
                                    <span class="badge bg-light text-dark border rounded-pill px-3">
                                        {{ $order->resi }}
                                    </span>
                                @else
                                    <form action="/seller/orders/{{ $order->id }}/update-resi" method="POST" class="d-flex gap-2">
                                        @csrf
                                        <input type="text" name="resi" class="form-control form-control-sm rounded-pill" placeholder="No. Resi" required>
                                        <button type="submit" class="btn btn-success btn-sm rounded-pill">Simpan</button>
                                    </form>
                                @endif
                            </td>
                            <td>
                                <p class="mb-0 fw-bold text-success">
                                    Rp {{ number_format(($order->product->harga ?? 0) * ($order->qty ?? 1),0,',','.') }}
                                </p>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="/seller/orders/{{ $order->id }}" class="btn btn-outline-success btn-sm rounded-pill">
                                        Detail
                                    </a>
                                    
                                    @if($order->status == 'pending')
                                        <form action="/seller/orders/{{ $order->id }}/update-status" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="diproses">
                                            <button type="submit" class="btn btn-success btn-sm rounded-pill">
                                                Proses
                                            </button>
                                        </form>
                                    @elseif($order->status == 'diproses')
                                        <form action="/seller/orders/{{ $order->id }}/update-status" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="dikirim">
                                            <button type="submit" class="btn btn-primary btn-sm rounded-pill">
                                                Kirim
                                            </button>
                                        </form>
                                    @elseif($order->status == 'dikirim')
                                        <form action="/seller/orders/{{ $order->id }}/update-status" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="selesai">
                                            <button type="submit" class="btn btn-info btn-sm rounded-pill">
                                                Selesai
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div style="font-size:48px;">📦</div>
                                <h5 class="fw-bold mt-3">Belum Ada Pesanan</h5>
                                <p class="text-muted">Pesanan akan muncul di sini</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>

@endsection



