<div class="product-price">
    @if($priceInfo['has_discount'])
        <span class="original-price text-muted text-decoration-line-through">
            {{ formatMoney($priceInfo['original_price']) }}
        </span>
        <span class="final-price text-danger fw-bold">
            {{ formatMoney($priceInfo['final_price']) }}
        </span>
        <span class="discount-badge bg-danger text-white px-2 py-1 rounded small">
            -{{ $priceInfo['discount_percentage'] }}%
        </span>
    @else
        <span class="final-price fw-bold">
            {{ formatMoney($product->price) }}
        </span>
    @endif
</div>