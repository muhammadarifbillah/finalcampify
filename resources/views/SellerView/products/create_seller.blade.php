@extends('SellerView.layouts.app_seller')

@section('content')

<div class="d-flex" style="min-height:100vh; background:#f9fafb;">


    {{-- MAIN CONTENT --}}
    <div class="flex-grow-1 p-4">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">TAMBAH PRODUK</h4>

            <a href="/seller/products" class="btn btn-light rounded-pill px-4">
                ← Kembali
            </a>
        </div>

        {{-- FORM CARD --}}
        <div class="card border-0 shadow-sm p-4" style="border-radius:16px; max-width:1000px;">

            <form action="{{ route('seller.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">

                    {{-- LEFT --}}
                    <div class="col-md-8">

                        {{-- NAMA PRODUK --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Produk</label>
                            <input type="text"
                                   name="nama_produk"
                                   class="form-control rounded-3"
                                   placeholder="Contoh: Tenda Camping 4 Orang"
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
                                           checked>

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
                                           value="sewa">

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
                                <option value="">Pilih Kategori</option>
                                <option value="tenda_tidur">Tenda & Perlengkapan Tidur</option>
                                <option value="tas">Tas & Carrier</option>
                                <option value="memasak">Peralatan Masak</option>
                                <option value="pencahayaan">Pencahayaan</option>
                                <option value="pakaian">Pakaian & Sepatu</option>
                                <option value="aksesoris">Aksesoris Outdoor</option>
                            </select>
                        </div>

                        {{-- HARGA --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Harga</label>
                            <input type="number"
                                   name="harga"
                                   class="form-control rounded-3"
                                   placeholder="Harga jual / harga sewa per hari">
                            <small class="text-muted">
                                Jika produk sewa, masukkan harga per hari
                            </small>
                        </div>

                        {{-- STOK --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Stok</label>
                            <input type="number"
                                   name="stok"
                                   class="form-control rounded-3"
                                   placeholder="Jumlah stok tersedia">
                        </div>

                        {{-- DESKRIPSI --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Deskripsi Produk</label>
                            <textarea name="deskripsi"
                                      class="form-control rounded-3"
                                      rows="5"
                                      placeholder="Jelaskan kondisi, spesifikasi, dan detail produk outdoor kamu..."></textarea>
                        </div>

                    </div>

                    {{-- RIGHT --}}
                    <div class="col-md-4">

                        <label class="form-label fw-semibold">Foto Produk</label>

                        <div class="border rounded-4 p-4 text-center bg-light"
                             style="min-height:280px; display:flex; flex-direction:column; justify-content:center;">

                            <div class="mb-3">
                                <span style="font-size:40px;">📷</span>
                            </div>

                            <p class="text-muted small mb-3">
                                Upload gambar utama produk
                            </p>

                            <input type="file"
                                   name="gambar"
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
                        Simpan Produk
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection

