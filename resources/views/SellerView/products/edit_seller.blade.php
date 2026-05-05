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
                    <a class="nav-link text-dark" href="/dashboard">
                        Dashboard
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a class="nav-link bg-success text-white rounded px-3 py-2" href="/products">
                        Kelola Produk
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a class="nav-link text-dark" href="/seller/orders">
                        Pesanan Baru
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a class="nav-link text-dark" href="/rentals">
                        Penyewaan Alat
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a class="nav-link text-dark" href="/chat">
                        Chat Pembeli
                    </a>
                </li>

            </ul>
        </div>

        {{-- BOTTOM --}}
        <div class="px-3 pb-4">
            <hr>
            <a class="nav-link text-muted" href="/store-profile/show">
                Profil Toko
            </a>
        </div>

    </div>

    {{-- MAIN CONTENT --}}
    <div class="flex-grow-1 p-4">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">EDIT PRODUK</h4>

            <a href="/seller/products" class="btn btn-light rounded-pill px-4">
                ← Kembali
            </a>
        </div>

        {{-- FORM CARD --}}
        <div class="card border-0 shadow-sm p-4" style="border-radius:16px; max-width:1000px;">

            <form action="/seller/products/{{ $product->id }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">

                    {{-- LEFT --}}
                    <div class="col-md-8">

                        {{-- NAMA PRODUK --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Produk</label>
                            <input type="text"
                                   name="nama_produk"
                                   value="{{ $product->nama_produk }}"
                                   class="form-control rounded-3"
                                   required>
                        </div>

                        {{-- JENIS PRODUK --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jenis Produk</label>

                            <div class="d-flex gap-3 mt-2">

                                {{-- JUAL --}}
                                <div class="form-check border rounded-3 px-4 py-3 flex-fill">
                                    <input class="form-check-input"
                                           type="radio"
                                           name="jenis_produk"
                                           id="jual"
                                           value="jual"
                                           {{ $product->jenis_produk == 'jual' ? 'checked' : '' }}>

                                    <label class="form-check-label fw-semibold ms-2" for="jual">
                                        Produk Jual
                                    </label>

                                    <div class="small text-muted mt-1">
                                        Untuk penjualan permanen
                                    </div>
                                </div>

                                {{-- SEWA --}}
                                <div class="form-check border rounded-3 px-4 py-3 flex-fill">
                                    <input class="form-check-input"
                                           type="radio"
                                           name="jenis_produk"
                                           id="sewa"
                                           value="sewa"
                                           {{ $product->jenis_produk == 'sewa' ? 'checked' : '' }}>

                                    <label class="form-check-label fw-semibold ms-2" for="sewa">
                                        Produk Sewa
                                    </label>

                                    <div class="small text-muted mt-1">
                                        Untuk penyewaan harian / mingguan
                                    </div>
                                </div>

                            </div>
                        </div>

                        {{-- KATEGORI --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kategori Alat Outdoor</label>
                            <select name="kategori" class="form-select rounded-3">
                                <option value="tenda" {{ $product->kategori == 'tenda' ? 'selected' : '' }}>Tenda</option>
                                <option value="carrier" {{ $product->kategori == 'carrier' ? 'selected' : '' }}>Carrier</option>
                                <option value="sleeping_bag" {{ $product->kategori == 'sleeping_bag' ? 'selected' : '' }}>Sleeping Bag</option>
                                <option value="kompor" {{ $product->kategori == 'kompor' ? 'selected' : '' }}>Kompor Portable</option>
                                <option value="matras" {{ $product->kategori == 'matras' ? 'selected' : '' }}>Matras</option>
                                <option value="sepatu" {{ $product->kategori == 'sepatu' ? 'selected' : '' }}>Sepatu Hiking</option>
                                <option value="lampu" {{ $product->kategori == 'lampu' ? 'selected' : '' }}>Lampu Camping</option>
                                <option value="nesting" {{ $product->kategori == 'nesting' ? 'selected' : '' }}>Nesting / Cook Set</option>
                            </select>
                        </div>

                        {{-- HARGA --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Harga</label>
                            <input type="number"
                                   name="harga"
                                   value="{{ $product->harga }}"
                                   class="form-control rounded-3">
                        </div>

                        {{-- STOK --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Stok</label>
                            <input type="number"
                                   name="stok"
                                   value="{{ $product->stok }}"
                                   class="form-control rounded-3">
                        </div>

                        {{-- DESKRIPSI --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Deskripsi Produk</label>
                            <textarea name="deskripsi"
                                      class="form-control rounded-3"
                                      rows="5">{{ $product->deskripsi }}</textarea>
                        </div>

                    </div>

                    {{-- RIGHT --}}
                    <div class="col-md-4">

                        <label class="form-label fw-semibold">Foto Produk</label>

                        <div class="border rounded-4 p-4 text-center bg-light"
                             style="min-height:320px; display:flex; flex-direction:column; justify-content:center;">

                            {{-- GAMBAR LAMA --}}
                            @if($product->gambar)
                                <img src="{{ asset('storage/'.$product->gambar) }}"
                                     class="img-fluid rounded mb-3"
                                     style="max-height:180px; object-fit:cover;">
                            @else
                                <div class="mb-3">
                                    <span style="font-size:40px;">📷</span>
                                </div>
                            @endif

                            <p class="text-muted small mb-3">
                                Upload gambar baru jika ingin mengganti
                            </p>

                            <input type="file"
                                   name="image"
                                   class="form-control rounded-3">

                            <small class="text-muted mt-3">
                                Format: JPG, PNG, JPEG
                            </small>

                        </div>

                    </div>

                </div>

                {{-- BUTTON --}}
                <div class="mt-4 d-flex justify-content-end gap-2">

                    <a href="/products" class="btn btn-light rounded-pill px-4">
                        Batal
                    </a>

                    <button type="submit"
                            class="btn text-white rounded-pill px-4"
                            style="background:#10B981;">
                        Update Produk
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection

