<div class="sidebar d-flex flex-column flex-shrink-0 p-3 bg-white border-right" style="width: 280px; height: 100vh; position: fixed; left: 0; top: 0; z-index: 1000; overflow-y: auto; transition: all 0.3s ease;">
    <div class="brand-section px-3 py-4 mb-3">
        <h3 class="brand-text m-0 fw-bold" style="background: linear-gradient(135deg, #10B981 0%, #059669 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">CAMPIFY.</h3>
        <small class="text-muted fw-semibold">SELLER HUB PORTAL</small>
    </div>

    <ul class="nav nav-pills flex-column mb-auto px-2">
        <li class="nav-item mb-2">
            <a href="{{ route('seller.dashboard') }}" class="nav-link py-3 rounded-4 d-flex align-items-center {{ request()->routeIs('seller.dashboard') ? 'active active-gradient' : 'text-dark hover-light' }}">
                <span class="me-3 fs-5">📊</span>
                <span class="fw-semibold">Dashboard</span>
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="{{ route('seller.products.index') }}" class="nav-link py-3 rounded-4 d-flex align-items-center {{ request()->routeIs('seller.products.*') ? 'active active-gradient' : 'text-dark hover-light' }}">
                <span class="me-3 fs-5">📦</span>
                <span class="fw-semibold">Kelola Produk</span>
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="/seller/ratings" class="nav-link py-3 rounded-4 d-flex align-items-center {{ request()->routeIs('seller.ratings.*') ? 'active active-gradient' : 'text-dark hover-light' }}">
                <span class="me-3 fs-5">⭐</span>
                <span class="fw-semibold">Rating Toko</span>
            </a>
        </li>

        <li class="mb-2">
            <button class="nav-link py-3 rounded-4 d-flex align-items-center justify-content-between w-100 {{ request()->is('seller/orders*') || request()->is('seller/rentals*') ? 'text-emerald fw-bold' : 'text-dark hover-light' }}" 
                    data-bs-toggle="collapse" data-bs-target="#transaksi-collapse" aria-expanded="{{ request()->is('seller/orders*') || request()->is('seller/rentals*') ? 'true' : 'false' }}">
                <div class="d-flex align-items-center">
                    <span class="me-3 fs-5">💰</span>
                    <span>Transaksi</span>
                </div>
                <span class="small opacity-50">▼</span>
            </button>
            <div class="collapse {{ request()->is('seller/orders*') || request()->is('seller/rentals*') ? 'show' : '' }} mt-1" id="transaksi-collapse">
                <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small ms-5">
                    <li class="mb-2"><a href="/seller/orders" class="link-dark d-inline-flex text-decoration-none rounded p-2 w-100 {{ request()->is('seller/orders*') ? 'fw-bold text-emerald' : 'text-muted' }}">🧾 Pesanan Baru</a></li>
                    <li class="mb-2"><a href="/seller/rentals" class="link-dark d-inline-flex text-decoration-none rounded p-2 w-100 {{ request()->is('seller/rentals*') ? 'fw-bold text-emerald' : 'text-muted' }}">🏕️ Penyewaan Alat</a></li>
                </ul>
            </div>
        </li>

        <li class="nav-item mb-2">
            <a href="/seller/chat" class="nav-link py-3 rounded-4 d-flex align-items-center {{ request()->is('seller/chat*') ? 'active active-gradient' : 'text-dark hover-light' }}">
                <span class="me-3 fs-5">💬</span>
                <span class="fw-semibold">Chat Pembeli</span>
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="{{ route('seller.reports.index') }}" class="nav-link py-3 rounded-4 d-flex align-items-center {{ request()->routeIs('seller.reports.*') ? 'active active-gradient' : 'text-dark hover-light' }}">
                <span class="me-3 fs-5">📈</span>
                <span class="fw-semibold">Laporan Bisnis</span>
            </a>
        </li>
    </ul>

    <hr class="mx-3 opacity-10">
    
    <div class="profile-section p-3">
        <a href="{{ route('seller.store-profile.index') }}" class="d-flex align-items-center link-dark text-decoration-none p-3 rounded-4 {{ request()->routeIs('seller.store-profile.*') ? 'bg-emerald-soft' : 'hover-light' }}">
            <div class="profile-avatar me-3 d-flex align-items-center justify-content-center text-white fw-bold rounded-circle" style="width: 45px; height: 45px; background: #10B981;">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
            <div class="overflow-hidden">
                <h6 class="mb-0 fw-bold text-truncate">{{ auth()->user()->name }}</h6>
                <small class="text-muted text-truncate d-block">Store Owner</small>
            </div>
        </a>
    </div>
</div>

<style>
    .hover-light:hover { background-color: #f8fafc; color: #10B981 !important; }
    .active-gradient { background: linear-gradient(135deg, #10B981 0%, #059669 100%) !important; color: white !important; box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.3); }
    .text-emerald { color: #10B981; }
    .bg-emerald-soft { background-color: #ecfdf5; border: 1px solid #d1fae5; }
    .sidebar { scrollbar-width: none; }
    .sidebar::-webkit-scrollbar { display: none; }
</style>
