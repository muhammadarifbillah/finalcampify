<nav class="navbar navbar-expand-lg navbar-custom mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/seller/dashboard">Campify</a>

        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div id="navbarNav" class="collapse navbar-collapse">

            <ul class="navbar-nav me-auto">
                @auth
                    <li class="nav-item"><a class="nav-link" href="/seller/products">Kelola Produk</a></li>
                    <li class="nav-item"><a class="nav-link" href="/seller/orders">Kelola Pesanan</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('seller.ratings.index') }}">Kelola Rating</a></li>
                @endauth
            </ul>

            <ul class="navbar-nav ms-auto">
                @guest
                    <li class="nav-item"><a class="nav-link" href="/login">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="/register">Register</a></li>
                @endguest

                @auth
                    <li class="nav-item"><span class="nav-link">Halo, {{ auth()->user()->name }}</span></li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="btn btn-light btn-sm">Logout</button>
                        </form>
                    </li>
                @endauth
            </ul>

        </div>
    </div>
</nav>


