@if(Auth::check())
@php
    $existingProductRating = \App\Models\ProductRating::where('product_id', $product->id)
        ->where('user_id', Auth::id())
        ->first();
    $existingStoreRating = \App\Models\StoreRating::where('store_id', $product->user_id)
        ->where('user_id', Auth::id())
        ->first();
@endphp


<div class="card border-0 shadow-sm p-4 mt-4" style="border-radius:16px;">
    <h6 class="fw-bold mb-3">BERI RATING</h6>
    
    <form method="POST" action="{{ route('ratings.product') }}">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">
        
        <div class="mb-3">
            <label class="form-label">Rating Produk</label>
            <div class="d-flex gap-2">
                @for($i = 1; $i <= 5; $i++)
                    <input type="radio" class="btn-check" name="rating" id="product_rating_{{ $i }}" value="{{ $i }}" 
                        {{ $existingProductRating && $existingProductRating->rating == $i ? 'checked' : '' }}>
                    <label class="btn btn-outline-warning" for="product_rating_{{ $i }}">{{ $i }} ★</label>
                @endfor
            </div>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Ulasan Produk</label>
            <textarea name="ulasan" class="form-control" rows="2" placeholder="Tulis ulasan Anda...">{{ $existingProductRating->ulasan ?? '' }}</textarea>
        </div>
        
        <button type="submit" class="btn btn-success rounded-pill px-4">
            {{ $existingProductRating ? 'Update Rating' : 'Kirim Rating' }}
        </button>
    </form>
    
    <hr class="my-4">
    
    <form method="POST" action="{{ route('ratings.store') }}">
        @csrf
        <input type="hidden" name="store_id" value="{{ $product->user_id }}">
        
        <div class="mb-3">
            <label class="form-label">Rating Toko</label>
            <div class="d-flex gap-2">
                @for($i = 1; $i <= 5; $i++)
                    <input type="radio" class="btn-check" name="rating" id="store_rating_{{ $i }}" value="{{ $i }}" 
                        {{ $existingStoreRating && $existingStoreRating->rating == $i ? 'checked' : '' }}>
                    <label class="btn btn-outline-warning" for="store_rating_{{ $i }}">{{ $i }} ★</label>
                @endfor
            </div>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Ulasan Toko</label>
            <textarea name="ulasan" class="form-control" rows="2" placeholder="Tulis ulasan untuk toko...">{{ $existingStoreRating->ulasan ?? '' }}</textarea>
        </div>
        
        <button type="submit" class="btn btn-primary rounded-pill px-4">
            {{ $existingStoreRating ? 'Update Rating Toko' : 'Kirim Rating Toko' }}
        </button>
    </form>
</div>
@else
<div class="card border-0 shadow-sm p-4 mt-4 text-center" style="border-radius:16px;">
    <p class="text-muted">Silakan <a href="{{ route('login') }}">login</a> untuk memberikan rating.</p>
</div>
@endif