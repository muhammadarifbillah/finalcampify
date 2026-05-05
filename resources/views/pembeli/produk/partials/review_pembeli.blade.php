<div class="bg-slate-50 rounded-2xl p-6 border border-slate-200">
    @php
        $existingReview = \App\Models\Pembeli\ProductRating_pembeli::where('user_id', auth()->id())
            ->where('product_id', $produk->id)
            ->where('order_id', $pesanan->id)
            ->first();
    @endphp

    @if($existingReview)
        <div class="space-y-2">
            <div class="flex items-center gap-2">
                <div class="flex text-yellow-400 text-lg">
                    @for($i=1; $i<=5; $i++)
                        <span>{!! $i <= $existingReview->rating ? '★' : '☆' !!}</span>
                    @endfor
                </div>
                <span class="text-xs font-bold text-slate-400">{{ $existingReview->created_at->format('d M Y') }}</span>
            </div>
            <p class="text-sm text-slate-700 font-medium">"{{ $existingReview->comment }}"</p>
        </div>
    @else
        <form action="{{ route('review.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="order_id" value="{{ $pesanan->id }}">
            <input type="hidden" name="product_id" value="{{ $produk->id }}">
            <input type="hidden" name="rating" id="rating-value-{{ $produk->id }}" value="5">
            
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-3">Berikan Rating</label>
                <div class="flex gap-2 text-3xl" id="star-container-{{ $produk->id }}">
                    @for($i=1; $i<=5; $i++)
                        <button type="button" 
                                onclick="setRating({{ $produk->id }}, {{ $i }})" 
                                class="star-btn-{{ $produk->id }} text-yellow-400 transition-transform hover:scale-110 focus:outline-none"
                                data-value="{{ $i }}">
                            ★
                        </button>
                    @endfor
                </div>
            </div>
            
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-500 uppercase">Tulis Ulasan</label>
                <textarea name="comment" rows="3" class="w-full rounded-2xl border-slate-200 text-sm focus:ring-emerald-500 focus:border-emerald-500" placeholder="Ceritakan pengalaman Anda menggunakan produk ini..."></textarea>
            </div>
            
            <button type="submit" class="w-full md:w-auto bg-emerald-600 text-white px-8 py-3 rounded-xl text-sm font-bold hover:bg-emerald-700 shadow-lg shadow-emerald-100 transition-all">
                Kirim Ulasan Sekarang
            </button>
        </form>

        <script>
            function setRating(productId, value) {
                document.getElementById('rating-value-' + productId).value = value;
                const stars = document.querySelectorAll('.star-btn-' + productId);
                stars.forEach((star, index) => {
                    if (index < value) {
                        star.innerHTML = '★';
                        star.classList.remove('text-slate-300');
                        star.classList.add('text-yellow-400');
                    } else {
                        star.innerHTML = '☆';
                        star.classList.remove('text-yellow-400');
                        star.classList.add('text-slate-300');
                    }
                });
            }
        </script>
    @endif
</div>
