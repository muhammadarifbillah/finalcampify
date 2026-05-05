@php 
    $routeName = request()->route()?->getName(); 
    $expiredRentals = collect();
    if(auth()->check() && auth()->user()->role === 'buyer') {
        $expiredRentals = \App\Models\Pembeli\OrderDetail_pembeli::whereHas('order', function($q) {
            $q->where('user_id', auth()->id())
              ->whereNotIn('status', ['selesai', 'dibatalkan', 'menunggu']);
        })
        ->where('type', 'rent')
        ->get()
        ->filter(function($detail) {
            if($detail->start_date && $detail->duration) {
                // Tambah durasi ke start date, set end of day, lalu cek apakah sekarang > end date
                $endDate = \Carbon\Carbon::parse($detail->start_date)->addDays($detail->duration)->endOfDay();
                return now()->greaterThan($endDate);
            }
            return false;
        });
    }
@endphp

@if($expiredRentals->count() > 0)
<div id="rentalAlertBanner" class="fixed top-0 left-0 right-0 bg-red-600 text-white text-center text-xs md:text-sm py-2.5 px-4 shadow-md z-[60] flex flex-col md:flex-row justify-center items-center gap-2">
    <span>⚠️ <strong>Peringatan:</strong> Ada {{ $expiredRentals->count() }} barang sewaan yang durasinya telah habis.</span>
    <a href="{{ route('orders.index') }}?tab=orders" class="underline font-bold hover:text-red-200 bg-white/20 px-3 py-1 rounded-full text-xs">Kembalikan Sekarang</a>
</div>
@endif

<nav id="mainNavbar" data-home="{{ request()->routeIs('home') ? 'true' : 'false' }}" data-transparent="{{ in_array($routeName, ['home', 'produk.rental', 'produk.index']) ? 'true' : 'false' }}" class="fixed {{ $expiredRentals->count() > 0 ? 'top-[68px] md:top-[40px]' : 'top-0' }} left-0 right-0 z-50 transition-all duration-300 {{ in_array($routeName, ['home', 'produk.rental', 'produk.index']) ? 'bg-transparent text-white' : 'bg-forest-800 text-white shadow-xl border-forest-700' }}">
  <div class="max-w-7xl mx-auto px-4 py-3 flex flex-col gap-3">

    <!-- TOP BAR -->
    <div class="flex items-center justify-between">

      <!-- Logo -->
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-forest-500 text-white flex items-center justify-center rounded-lg font-bold text-xl">
          C
        </div>
        <span class="font-semibold text-lg text-white">Campify</span>
      </div>

      <!-- Search -->
      <div class="hidden md:flex flex-1 mx-6">
        <form action="{{ route('produk.search') }}" method="GET" class="w-full">
          <div class="w-full bg-white/20 rounded-full px-4 py-2 flex items-center border border-transparent focus-within:border-white/50 transition">
            <span class="mr-2 text-white">🔍</span>
            <input type="text"
              name="q"
              placeholder="Cari tenda, carrier, atau alat masak..."
              class="bg-transparent w-full focus:outline-none text-sm text-white placeholder-white/70">
          </div>
        </form>
      </div>

      <!-- Icons -->
      <div class="flex items-center gap-4 text-xl text-white">
        <a href="{{ route('chat.index') }}" class="hover:text-forest-300 transition">💬</a>
        <a href="{{ route('wishlist.index') }}" class="hover:text-forest-300 transition relative">
          🤍
          @if(auth()->check() && ($wishlistCount ?? 0) > 0)
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">
              {{ $wishlistCount ?? 0 }}
            </span>
          @endif
        </a>
        <a href="{{ route('cart.index') }}" class="hover:text-forest-300 transition relative">
          🛒
          @if(auth()->check() && ($cartCount ?? 0) > 0)
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">
              {{ $cartCount ?? 0 }}
            </span>
          @endif
        </a>

        <div class="flex items-center gap-2 border-l border-white/30 text-white pl-4">
          @auth
            <div class="relative">
              <button onclick="toggleDropdown()" class="flex items-center gap-2 hover:text-forest-300 transition text-white">
                <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center text-gray-700 font-bold text-sm">
                  {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <span class="text-sm font-medium">{{ auth()->user()->name }}</span>
                <span class="text-xs">▼</span>
              </button>
              
              <!-- Dropdown Menu -->
              <div id="userDropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border hidden text-gray-800">
                <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-forest-50">Profile</a>
                
                <hr class="my-1 border-gray-200">
                <form action="{{ route('logout') }}" method="POST" class="block">
                  @csrf
                  <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Logout</button>
                </form>
              </div>
            </div>
          @else
            <a href="{{ route('login') }}" class="text-sm font-medium hover:text-forest-300 transition text-white">Login</a>
          @endauth
        </div>
      </div>
    </div>

    <!-- MENU -->
    <div class="flex gap-6 text-sm font-medium text-white border-t border-white/20 pt-3">
      <a href="{{ route('home') }}" class="hover:text-forest-300 transition">Home</a>
      <a href="{{ route('produk.rental') }}" class="hover:text-forest-300 transition">Sewa</a>
      <a href="{{ route('produk.index') }}" class="hover:text-forest-300 transition">Beli</a>
      <a href="{{ route('articles.index') }}" class="hover:text-forest-300 transition">Artikel</a>
    </div>

  </div>
</nav>

<script>
function toggleDropdown() {
  const dropdown = document.getElementById('userDropdown');
  dropdown.classList.toggle('hidden');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
  const dropdown = document.getElementById('userDropdown');
  const button = event.target.closest('button');
  
  if (!button || !button.onclick || typeof button.onclick !== 'function' || button.onclick.toString().indexOf('toggleDropdown') === -1) {
    if (dropdown && !dropdown.classList.contains('hidden')) {
      dropdown.classList.add('hidden');
    }
  }
});

window.addEventListener('scroll', function () {
  const navbar = document.getElementById('mainNavbar');
  const isTransparent = navbar.dataset.transparent === 'true';

  if (isTransparent) {
    if (window.scrollY > 20) {
      navbar.classList.add('bg-forest-800', 'shadow-xl', 'border-forest-700');
      navbar.classList.remove('bg-transparent');
    } else {
      navbar.classList.remove('bg-forest-800', 'shadow-xl', 'border-forest-700');
      navbar.classList.add('bg-transparent');
    }
  } else {
    if (window.scrollY > 20) {
      navbar.classList.add('shadow-2xl');
    } else {
      navbar.classList.remove('shadow-2xl');
    }
  }
});
</script>
