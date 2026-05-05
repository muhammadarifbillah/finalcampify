@extends('layouts.app_pembeli')

@section('content')
<div class="py-12 max-w-3xl mx-auto px-4">

  <a href="/cart" class="flex items-center space-x-2 text-forest-600 mb-8">
    ← <span class="text-xs font-bold uppercase">Kembali ke Keranjang</span>
  </a>

  <div class="bg-white rounded-3xl border p-8 shadow-sm">

    <!-- STEP 1 -->
    <h2 class="text-xl font-bold mb-6 uppercase">Alamat Pengiriman</h2>

    <div class="p-6 bg-forest-50 rounded-xl mb-6">
      <p class="font-bold">{{ $user->name }}</p>
      <p class="text-sm">{{ $user->address }}</p>
      <p class="text-sm">{{ $user->phone }}</p>
    </div>

    <h2 class="text-xl font-bold mb-4 uppercase">Metode Pengiriman</h2>

    <form action="/checkout/process" method="POST">
      @csrf

      <div class="space-y-4 mb-8">
        <label class="block border p-4 rounded-xl cursor-pointer">
          <input type="radio" name="shipping" value="express" checked>
          Kurir Ekspress (1-2 hari)
        </label>

        <label class="block border p-4 rounded-xl cursor-pointer">
          <input type="radio" name="shipping" value="standard">
          Standar (3-5 hari)
        </label>
      </div>

      <!-- STEP 2 -->
      <h2 class="text-xl font-bold mb-4 uppercase">Metode Pembayaran</h2>

      <div class="space-y-4 mb-8">
        <label class="block border p-4 rounded-xl cursor-pointer">
          <input type="radio" name="payment" value="qris" checked>
          QRIS / E-Wallet
        </label>

        <label class="block border p-4 rounded-xl cursor-pointer">
          <input type="radio" name="payment" value="va">
          Virtual Account
        </label>
      </div>

      <!-- SUMMARY -->
      @php
    $subtotal = 0;
    foreach(session('cart', []) as $item) {
        $subtotal += ($item['harga'] ?? 0) * ($item['qty'] ?? 1);
    }
@endphp

<div class="bg-forest-50 p-6 rounded-xl mb-6">
  <div class="flex justify-between">
    <span>Subtotal</span>
    <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
  </div>
  <div class="flex justify-between mt-2">
    <span>Total</span>
    <span class="font-bold">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
  </div>
</div>

      <button class="w-full bg-forest-600 text-white py-3 rounded-xl">
        Bayar Sekarang
      </button>

    </form>
  </div>
</div>
@endsection