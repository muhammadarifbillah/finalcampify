@extends('SellerView.layouts.app_seller')

@section('content')
<div class="dashboard-header mb-5">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h2 class="fw-bold m-0 text-dark">Tambah Produk Baru</h2>
            <p class="text-muted">Lengkapi informasi di bawah untuk mulai menawarkan barang Anda.</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('seller.products.index') }}" class="btn btn-light rounded-pill px-4 border">
                <i class="bi bi-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>
</div>

<form action="{{ route('seller.products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row g-4">
        {{-- LEFT COLUMN: Basic Info --}}
        <div class="col-md-8">
            <div class="card card-modern border-0 p-5 mb-4">
                <h5 class="fw-bold mb-4">Informasi Produk</h5>
                
                <div class="mb-4">
                    <label class="form-label fw-bold text-muted small text-uppercase ls-1">Nama Produk</label>
                    <input type="text" name="nama_produk" class="form-control form-control-lg border-0 bg-light rounded-3 px-4" placeholder="Contoh: Tenda Dome Kapasitas 4 Orang" required>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted small text-uppercase ls-1">Kategori</label>
                        <select name="kategori" class="form-select form-select-lg border-0 bg-light rounded-3 px-4" required>
                            <option value="">Pilih Kategori</option>
                            <option value="tenda">Tenda</option>
                            <option value="tas_gunung">Tas Gunung (Carrier)</option>
                            <option value="sepatu">Sepatu Outdoor</option>
                            <option value="alat_masak">Alat Masak</option>
                            <option value="penerangan">Penerangan</option>
                            <option value="aksesoris">Aksesoris</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted small text-uppercase ls-1">Jenis Layanan</label>
                        <select name="jenis_produk" class="form-select form-select-lg border-0 bg-light rounded-3 px-4" required>
                            <option value="jual">Penjualan Barang</option>
                            <option value="sewa">Penyewaan Alat</option>
                        </select>
                    </div>
                </div>

                <div class="mb-0">
                    <label class="form-label fw-bold text-muted small text-uppercase ls-1">Deskripsi Lengkap</label>
                    <textarea name="deskripsi" rows="6" class="form-control border-0 bg-light rounded-3 px-4 py-3" placeholder="Jelaskan detail produk, spesifikasi, dan kondisi barang..." required></textarea>
                </div>
            </div>

            <div class="card card-modern border-0 p-5">
                <h5 class="fw-bold mb-4">Harga & Stok</h5>
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted small text-uppercase ls-1">Harga (Rp)</label>
                        <div class="input-group">
                            <span class="input-group-text border-0 bg-light rounded-start-3 px-3 fw-bold">Rp</span>
                            <input type="number" name="harga" class="form-control form-control-lg border-0 bg-light rounded-end-3 px-4" placeholder="0" required>
                        </div>
                        <small class="text-muted mt-2 d-block">Harga per unit (atau per hari jika sewa)</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted small text-uppercase ls-1">Jumlah Stok</label>
                        <input type="number" name="stok" class="form-control form-control-lg border-0 bg-light rounded-3 px-4" placeholder="0" required>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: Media --}}
        <div class="col-md-4">
            <div class="card card-modern border-0 p-5 mb-4 text-center">
                <h5 class="fw-bold mb-4 text-start">Foto Produk</h5>
                
                <div class="upload-area bg-light rounded-4 p-4 border-2 border-dashed d-flex flex-column align-items-center justify-content-center" style="min-height: 250px; border-color: #cbd5e1 !important;">
                    <i class="bi bi-cloud-arrow-up fs-1 text-emerald mb-3"></i>
                    <p class="small text-muted mb-3">Pilih foto produk terbaik Anda<br>(Max 2MB, JPG/PNG)</p>
                    <input type="file" name="gambar" class="form-control form-control-sm border-0" accept="image/*" required>
                </div>
            </div>

            <div class="card card-modern border-0 p-4 bg-emerald text-white" style="background: linear-gradient(135deg, #10B981 0%, #059669 100%);">
                <h6 class="fw-bold mb-3"><i class="bi bi-info-circle me-2"></i>Tips Seller</h6>
                <ul class="small opacity-90 ps-3 mb-0">
                    <li class="mb-2">Gunakan foto asli dengan pencahayaan terang.</li>
                    <li class="mb-2">Berikan deskripsi spesifikasi teknis yang jelas.</li>
                    <li>Cantumkan harga kompetitif untuk menarik pembeli.</li>
                </ul>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-emerald w-100 py-3 rounded-4 shadow-sm fw-bold">
                    <i class="bi bi-check-lg me-2"></i>Simpan Produk
                </button>
            </div>
        </div>
    </div>
</form>

<style>
    .ls-1 { letter-spacing: 1px; }
    .text-emerald { color: #10B981; }
</style>
@endsection
