@extends('SellerView.layouts.app_seller')

@section('content')
<div class="dashboard-header mb-5">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h2 class="fw-bold m-0 text-dark">Update Status Sewa</h2>
            <p class="text-muted">Kelola status aktif/selesai untuk unit perlengkapan Anda.</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('seller.rentals.index') }}" class="btn btn-light rounded-pill px-4 border shadow-sm">
                <i class="bi bi-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-7">
        <div class="card card-modern border-0 shadow-sm p-4" style="border-radius:16px;">
            <div class="mb-4 pb-3 border-bottom">
                <h5 class="fw-bold mb-1 text-dark">Transaksi Sewa #{{ $rental->id }}</h5>
                <p class="text-muted small mb-0">Produk: <strong class="text-primary">{{ $rental->product->nama_produk ?? '-' }}</strong></p>
            </div>

            <form action="{{ route('seller.rentals.update', $rental->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="mb-4">
                    <label class="form-label fw-bold text-muted small text-uppercase ls-1">Status Penyewaan</label>
                    <select name="status" class="form-select border-0 bg-light rounded-3 px-3 py-2 shadow-sm">
                        <option value="pending" {{ $rental->status == 'pending' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                        <option value="active" {{ $rental->status == 'active' ? 'selected' : '' }}>Aktif (Sedang Disewa)</option>
                        <option value="completed" {{ $rental->status == 'completed' ? 'selected' : '' }}>Selesai (Sudah Dikembalikan)</option>
                        <option value="cancelled" {{ $rental->status == 'cancelled' ? 'selected' : '' }}>Batalkan Sewa</option>
                    </select>
                </div>
                
                <div class="mb-4" id="photo_handover_container" style="display: none;">
                    <label class="form-label fw-bold text-muted small text-uppercase ls-1">Foto Kondisi Realtime Barang (Wajib jika Aktif)</label>
                    <input type="file" name="foto_kondisi" id="foto_kondisi" class="form-control border-0 bg-light rounded-3 px-3 py-2 shadow-sm" accept="image/*">
                    <small class="text-muted mt-2 d-block">Unggah foto kondisi terkini barang sebelum diserahkan ke penyewa.</small>
                    @if($rental->condition_photo_handover)
                        <div class="mt-2">
                            <small class="text-success d-block mb-1">Foto saat ini:</small>
                            <img src="{{ asset($rental->condition_photo_handover) }}" class="img-fluid rounded-3 border" style="max-height: 150px;">
                        </div>
                    @endif
                </div>

                <div class="p-3 bg-primary bg-opacity-10 rounded-4 mb-4 border border-primary border-opacity-10">
                    <div class="d-flex align-items-center gap-2 text-primary">
                        <i class="bi bi-info-circle-fill"></i>
                        <small class="fw-bold">Catatan:</small>
                    </div>
                    <p class="small text-dark mb-0 mt-1">Pastikan unit telah diterima kembali dengan kondisi baik sebelum mengubah status menjadi <strong>Selesai</strong>.</p>
                </div>

                <div class="mt-2">
                    <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow-sm" style="background: #3b82f6;">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card card-modern border-0 p-4 shadow-sm h-100">
            <h6 class="fw-bold mb-3 text-muted small text-uppercase ls-1">Detail Singkat Sewa</h6>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted small">Penyewa:</span>
                <span class="text-dark small fw-bold">{{ $rental->user->name ?? 'User' }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted small">Durasi:</span>
                <span class="text-dark small fw-bold">{{ $rental->duration }} Hari</span>
            </div>
            <div class="d-flex justify-content-between mb-4">
                <span class="text-muted small">Total Biaya:</span>
                <span class="text-primary small fw-bold">Rp {{ number_format($rental->price * $rental->duration, 0, ',', '.') }}</span>
            </div>
            <hr>
            <div class="text-center py-2">
                <p class="small text-muted mb-0">Butuh koordinasi dengan penyewa?</p>
                <a href="/seller/chat?user={{ $rental->user_id }}" class="btn btn-link text-primary p-0 fw-bold text-decoration-none small">Hubungi via Chat</a>
            </div>
        </div>
    </div>
</div>

<style>
    .ls-1 { letter-spacing: 1px; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.querySelector('select[name="status"]');
    const photoContainer = document.getElementById('photo_handover_container');
    const photoInput = document.getElementById('foto_kondisi');
    const hasPhoto = {{ $rental->condition_photo_handover ? 'true' : 'false' }};

    function togglePhotoInput() {
        if (statusSelect.value === 'active') {
            photoContainer.style.display = 'block';
            if (!hasPhoto) {
                photoInput.setAttribute('required', 'required');
            }
        } else {
            photoContainer.style.display = 'none';
            photoInput.removeAttribute('required');
        }
    }

    statusSelect.addEventListener('change', togglePhotoInput);
    togglePhotoInput(); // Initial run
});
</script>
@endsection
