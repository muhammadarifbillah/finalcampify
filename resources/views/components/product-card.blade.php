<div class="card-outdoor group bg-white rounded-3xl border border-gray-100 overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition duration-300">

  @php
    $isBuy = $product->jenis_produk === 'jual' || ($product->buy_price > 0 && $product->rent_price <= 0);
  @endphp

  <!-- IMAGE -->
  <div class="relative aspect-[4/3] overflow-hidden">
    <a href="{{ $isBuy 
        ? route('produk.detail.buy', $product->id) 
        : route('produk.detail.rent', $product->id) }}">
        
      <img
  src="{{ $product->image_url }}"
  alt="{{ $product->name }}"
  class="w-full h-full object-cover transition duration-500 group-hover:scale-110"
>
    </a>

    <!-- TOP -->
    <div class="absolute inset-x-0 top-3 px-4 flex justify-between items-center">

      <!-- BADGE -->
      @if($isBuy)
        <span class="bg-slate-100 text-slate-700 text-[10px] px-3 py-1 rounded-full font-semibold">
          Beli
        </span>
      @else
        <span class="bg-emerald-600 text-white text-[10px] px-3 py-1 rounded-full font-semibold">
          Sewa
        </span>
      @endif

      <!-- WISHLIST -->
      <form action="{{ route('wishlist.toggle') }}" method="POST">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">
        <button class="w-9 h-9 rounded-full bg-white/90 shadow flex items-center justify-center
          {{ $isWishlisted ?? false ? 'text-red-500' : 'text-gray-400 hover:text-red-500' }}">
          ❤️
        </button>
      </form>

    </div>

    <!-- RATING -->
    <div class="absolute bottom-3 left-3 bg-white/90 px-2 py-1 rounded-md border flex items-center gap-1 shadow-sm">
      <span class="text-yellow-400 text-xs">★</span>
      <span class="text-[11px] font-semibold text-gray-800">
        {{ $product->rating ?? 4.5 }}
      </span>
    </div>
  </div>

  <!-- CONTENT -->
  <div class="p-4">

    <!-- CATEGORY -->
    <span class="text-[11px] font-semibold text-green-600 bg-green-50 px-2 py-1 rounded-full">
      {{ $product->category }}
    </span>

    <!-- TITLE -->
    <a href="{{ $isBuy 
        ? route('produk.detail.buy', $product->id) 
        : route('produk.detail.rent', $product->id) }}">
        
      <h3 class="text-gray-900 font-semibold text-sm mt-2 line-clamp-2 hover:text-green-600 transition">
        {{ $product->name }}
      </h3>
    </a>

    <!-- PRICE -->
    <div class="mt-3">
      @if($isBuy)
        <div class="text-sm font-bold text-gray-800">
          Rp {{ number_format($product->buy_price) }}
        </div>
      @else
        <div class="text-sm font-bold text-green-600">
          Rp {{ number_format($product->rent_price) }}/hari
        </div>
      @endif
    </div>

    <!-- BUTTONS -->
    @if($isBuy)
      <div class="mt-4 flex flex-col gap-2">
        <form action="{{ route('cart.add') }}" method="POST">
          @csrf
          <input type="hidden" name="product_id" value="{{ $product->id }}">
          <input type="hidden" name="type" value="buy">
          <button type="submit" class="w-full py-2 bg-green-600 hover:bg-green-700 text-white rounded-xl text-xs font-semibold flex items-center justify-center transition">Masukkan Keranjang</button>
        </form>
        <a href="{{ route('produk.detail.buy', $product->id) }}" class="w-full py-2 bg-emerald-50 hover:bg-emerald-600 hover:text-white text-emerald-700 rounded-xl text-xs font-semibold flex items-center justify-center transition">Beli Sekarang</a>
      </div>
    @else
      <a href="{{ route('produk.detail.rent', $product->id) }}" class="mt-4 w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-[11px] font-bold uppercase tracking-wider flex items-center justify-center transition shadow-lg shadow-emerald-50">Sewa Sekarang</a>
    @endif

  </div>
</div>