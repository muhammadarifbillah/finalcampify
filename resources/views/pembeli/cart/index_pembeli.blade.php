@extends('layouts.app_pembeli')

@section('content')
<div class="py-12 max-w-7xl mx-auto px-4 bg-gray-50 min-h-screen">
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-10 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 tracking-tight uppercase">Keranjang Belanja</h1>
            <p class="text-sm text-slate-500">Pilih perlengkapan yang ingin Anda bawa berpetualang hari ini.</p>
        </div>
        @if(!$cart->isEmpty())
        <div class="flex items-center gap-3 bg-white px-6 py-4 rounded-3xl shadow-sm border border-gray-200 self-end md:self-auto">
            <input type="checkbox" id="select-all" class="w-6 h-6 rounded border-gray-300 text-green-600 focus:ring-green-500 cursor-pointer">
            <label for="select-all" class="text-sm font-bold text-slate-700 cursor-pointer uppercase tracking-widest">Pilih Semua</label>
        </div>
        @endif
    </div>

    @if($cart->isEmpty())
        <div class="text-center py-24 bg-white rounded-3xl shadow-sm border border-gray-100">
            <div class="mx-auto h-24 w-24 bg-green-50 rounded-full flex items-center justify-center text-5xl mb-6">🛒</div>
            <h2 class="text-2xl font-bold text-slate-900 mb-2">Keranjang Anda Kosong</h2>
            <p class="text-slate-500 mb-8 max-w-xs mx-auto">Sepertinya Anda belum memilih perlengkapan camping. Yuk, mulai jelajahi produk kami!</p>
            <a href="{{ route('produk.index') }}" class="inline-flex items-center gap-3 bg-green-600 hover:bg-green-700 text-white px-8 py-4 rounded-2xl font-bold text-sm uppercase tracking-widest transition-all">
                Mulai Belanja
            </a>
        </div>
    @else

    <form action="{{ route('checkout.index') }}" method="GET" id="cart-form">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

            <!-- ITEMS LIST -->
            <div class="lg:col-span-2 space-y-6">
                @foreach($cart as $item)
                    @php
                        $price = $item->type === 'buy'
                            ? ($item->product->buy_price ?? 0)
                            : ($item->product->rent_price ?? 0) * $item->duration;
                        $itemTotal = $price * $item->qty;
                    @endphp

                    <div class="bg-white p-6 md:p-8 rounded-3xl shadow-sm border border-gray-200 transition-all hover:shadow-md flex items-center gap-6 group relative overflow-hidden">
                        <!-- Selection Area -->
                        <div class="flex-shrink-0 flex items-center justify-center pr-2">
                            <input type="checkbox" name="selected_items[]" value="{{ $item->id }}" 
                                   class="cart-checkbox w-7 h-7 rounded border-gray-300 text-green-600 focus:ring-green-500 cursor-pointer"
                                   data-price="{{ $itemTotal }}" checked>
                        </div>

                        <!-- Product Image -->
                        <div class="relative h-28 w-28 md:h-36 md:w-36 flex-shrink-0 overflow-hidden rounded-2xl shadow-sm border border-gray-100">
                            <img src="{{ $item->product->image_url }}" 
                                 alt="{{ $item->product->name }}"
                                 class="h-full w-full object-cover">
                            <div class="absolute bottom-2 left-1/2 -translate-x-1/2 bg-slate-900/90 text-white text-[9px] font-bold px-3 py-1 rounded-full uppercase tracking-tighter shadow-lg">
                                {{ $item->type === 'buy' ? 'Beli' : 'Sewa' }}
                            </div>
                        </div>

                        <!-- Product Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-col h-full justify-between">
                                <div class="mb-4">
                                    <div class="flex justify-between items-start gap-4">
                                        <h3 class="text-xl font-bold text-slate-900 leading-tight truncate">{{ $item->product->name }}</h3>
                                        <button type="button" onclick="removeItem({{ $item->id }})" class="h-10 w-10 flex-shrink-0 flex items-center justify-center rounded-xl bg-red-50 text-red-400 hover:bg-red-500 hover:text-white transition-all">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mt-2">
                                        @if($item->type === 'rent')
                                            {{ $item->duration }} HARI • MULAI {{ \Carbon\Carbon::parse($item->start_date)->format('d M Y') }}
                                        @else
                                            PEMBELIAN PERALATAN
                                        @endif
                                    </p>
                                </div>

                                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-6">
                                    <!-- Controls -->
                                    <div class="flex flex-col gap-2">
                                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Kuantitas</p>
                                        <div class="flex items-center bg-gray-100 rounded-xl p-1 border border-gray-200 w-fit">
                                            @if($item->type === 'buy')
                                                <button type="button" onclick="updateQty({{ $item->id }}, {{ $item->qty - 1 }})" class="h-10 w-10 flex items-center justify-center rounded bg-white hover:bg-green-600 hover:text-white transition-all text-slate-600 font-bold">-</button>
                                                <span class="w-12 text-center font-bold text-slate-800">{{ $item->qty }}</span>
                                                <button type="button" onclick="updateQty({{ $item->id }}, {{ $item->qty + 1 }})" class="h-10 w-10 flex items-center justify-center rounded bg-white hover:bg-green-600 hover:text-white transition-all text-slate-600 font-bold">+</button>
                                            @else
                                                <button type="button" onclick="updateDuration({{ $item->id }}, {{ $item->duration - 1 }})" class="h-10 w-10 flex items-center justify-center rounded bg-white hover:bg-green-600 hover:text-white transition-all text-slate-600 font-bold">-</button>
                                                <div class="flex items-center px-3">
                                                    <span class="font-bold text-slate-800">{{ $item->duration }}</span>
                                                    <span class="text-[9px] font-bold text-slate-400 uppercase ml-2">Hari</span>
                                                </div>
                                                <button type="button" onclick="updateDuration({{ $item->id }}, {{ $item->duration + 1 }})" class="h-10 w-10 flex items-center justify-center rounded bg-white hover:bg-green-600 hover:text-white transition-all text-slate-600 font-bold">+</button>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="text-right flex flex-col items-end">
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Subtotal</p>
                                        <p class="text-2xl font-bold text-green-600 leading-none">Rp {{ number_format($itemTotal, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- SUMMARY SIDEBAR -->
            <div class="h-fit lg:sticky lg:top-24">
                <div class="bg-slate-900 p-8 rounded-3xl shadow-xl text-white">
                    <h2 class="text-xl font-bold mb-8">Ringkasan</h2>

                    <div class="space-y-6 mb-8">
                        <div class="flex justify-between items-center">
                            <span class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em]">Item Terpilih</span>
                            <span id="selected-count" class="font-bold text-white text-xl">0</span>
                        </div>
                        <div class="flex justify-between items-center pt-6 border-t border-slate-800">
                            <div class="flex flex-col gap-1 w-full">
                                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em]">Estimasi Total</span>
                                <span id="display-total" class="text-4xl font-bold text-white tabular-nums">Rp 0</span>
                            </div>
                        </div>
                    </div>

                    <button type="submit" id="checkout-btn" disabled class="w-full bg-green-500 disabled:bg-slate-800 disabled:text-slate-600 hover:bg-green-400 text-white py-4 rounded-2xl font-bold text-base transition-all flex items-center justify-center shadow-lg">
                        Checkout Terpilih
                    </button>
                </div>
            </div>
        </div>
    </form>
    @endif
</div>

<!-- Hidden form for update/remove actions -->
<form id="action-form" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="_method" id="action-method" value="POST">
    <input type="hidden" name="quantity" id="action-qty">
    <input type="hidden" name="duration" id="action-duration">
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.cart-checkbox');
        const displayTotal = document.getElementById('display-total');
        const selectedCount = document.getElementById('selected-count');
        const checkoutBtn = document.getElementById('checkout-btn');

        function updateSummary() {
            let total = 0;
            let count = 0;
            checkboxes.forEach(cb => {
                if(cb.checked) {
                    total += parseFloat(cb.dataset.price);
                    count++;
                }
            });
            displayTotal.textContent = 'Rp ' + total.toLocaleString('id-ID');
            selectedCount.textContent = count;
            checkoutBtn.disabled = count === 0;
            
            if(selectAll) {
                selectAll.checked = count === checkboxes.length && checkboxes.length > 0;
            }
        }

        if(selectAll) {
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = this.checked);
                updateSummary();
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateSummary);
        });

        updateSummary();
    });

    function updateQty(id, val) {
        if(val < 1) return;
        const form = document.getElementById('action-form');
        form.action = `/cart/update/${id}`;
        document.getElementById('action-method').value = 'POST';
        document.getElementById('action-qty').value = val;
        form.submit();
    }

    function updateDuration(id, val) {
        if(val < 1) return;
        const form = document.getElementById('action-form');
        form.action = `/cart/update/${id}`;
        document.getElementById('action-method').value = 'POST';
        document.getElementById('action-duration').value = val;
        form.submit();
    }

    function removeItem(id) {
        if(!confirm('Hapus produk dari keranjang?')) return;
        const form = document.getElementById('action-form');
        form.action = `/cart/remove/${id}`;
        document.getElementById('action-method').value = 'DELETE';
        form.submit();
    }
</script>
@endsection