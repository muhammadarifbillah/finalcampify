<nav id="mainNavbar" data-home="{{ request()->routeIs('home') ? 'true' : 'false' }}" data-transparent="{{ in_array(request()->route()->getName(), ['home', 'produk.rental', 'produk.index']) ? 'true' : 'false' }}" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 {{ in_array(request()->route()->getName(), ['home', 'produk.rental', 'produk.index']) ? 'bg-transparent text-white' : 'bg-forest-950 text-white shadow-xl border-forest-800' }}">
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
          <div class="w-full bg-white/20 rounded-full px-4 py-2 flex items-center">
            <span class="mr-2 text-white">🔍</span>
            <input type="text"
              name="q"
              placeholder="Cari tenda, carrier, atau alat masak..."
              class="bg-transparent w-full focus:outline-none text-sm text-white placeholder-white/70">
          </div>
        </form>
      </div>

      <!-- Icons -->
      <div class="flex items-center gap-4 text-xl text-forest-500">
        <a href="{{ route('chat.index') }}" class="hover:text-forest-400 transition">💬</a>
        <a href="{{ route('wishlist.index') }}" class="hover:text-forest-400 transition relative">
          🤍
          @if(auth()->check() && ($wishlistCount ?? 0) > 0)
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">
              {{ $wishlistCount ?? 0 }}
            </span>
          @endif
        </a>
        <a href="{{ route('cart.index') }}" class="hover:text-forest-400 transition relative">
          🛒
          @if(auth()->check() && ($cartCount ?? 0) > 0)
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">
              {{ $cartCount ?? 0 }}
            </span>
          @endif
        </a>

        <div class="flex items-center gap-2 border-forest-500/30 text-forest-500 pl-4">
          @auth
            <div class="relative">
              <button onclick="toggleDropdown()" class="flex items-center gap-2 hover:text-forest-400 transition text-forest-500">
                <div class="w-8 h-8 bg-gray-300 rounded-full"></div>
                <span class="text-sm font-medium">{{ auth()->user()->name }}</span>
                <span class="text-xs">▼</span>
              </button>
              
              <!-- Dropdown Menu -->
              <div id="userDropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border hidden">
                <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                
                <hr class="my-1">
                <form action="{{ route('logout') }}" method="POST" class="block">
                  @csrf
                  <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Logout</button>
                </form>
              </div>
            </div>
          @else
            <a href="{{ route('login') }}" class="text-sm font-medium hover:text-forest-400 transition text-forest-500">Login</a>
          @endauth
        </div>
      </div>
    </div>

    <!-- MENU -->
    <div class="flex gap-6 text-sm font-medium text-white border-t border-white/20 pt-3">
      <a href="{{ route('home') }}" class="hover:text-green-300">Home</a>
      <a href="{{ route('produk.rental') }}" class="hover:text-green-300">Sewa</a>
      <a href="{{ route('produk.index') }}" class="hover:text-green-300">Beli</a>
      <a href="{{ route('articles.index') }}" class="hover:text-green-300">Artikel</a>
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
      navbar.classList.add('bg-forest-950', 'shadow-xl', 'border-forest-800');
      navbar.classList.remove('bg-transparent');
    } else {
      navbar.classList.remove('bg-forest-950', 'shadow-xl', 'border-forest-800');
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