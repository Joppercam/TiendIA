@props(['product', 'showQuantity' => true])

<form action="{{ route('cart.add') }}" method="POST" {{ $attributes }}>
    @csrf
    <input type="hidden" name="product_id" value="{{ $product->id }}">
    
    @if($showQuantity)
        <div class="flex items-center mb-4">
            <label for="quantity-{{ $product->id }}" class="mr-3 text-sm font-medium text-gray-700">Cantidad:</label>
            <div class="flex items-center border border-gray-300 rounded-md">
                <button type="button" class="quantity-btn minus px-2 py-1 text-gray-500 focus:outline-none" data-input="quantity-{{ $product->id }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                    </svg>
                </button>
                <input id="quantity-{{ $product->id }}" name="quantity" type="number" min="1" max="99" value="1" class="w-14 border-0 text-center focus:ring-0">
                <button type="button" class="quantity-btn plus px-2 py-1 text-gray-500 focus:outline-none" data-input="quantity-{{ $product->id }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </button>
            </div>
        </div>
    @else
        <input type="hidden" name="quantity" value="1">
    @endif
    
    <button type="submit" class="w-full flex items-center justify-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
        </svg>
        {{ $slot ?? 'Añadir al Carrito' }}
    </button>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const minusBtn = document.querySelector('[data-input="quantity-{{ $product->id }}"].minus');
            const plusBtn = document.querySelector('[data-input="quantity-{{ $product->id }}"].plus');
            const input = document.getElementById('quantity-{{ $product->id }}');
            
            if (minusBtn && plusBtn && input) {
                minusBtn.addEventListener('click', function() {
                    const value = parseInt(input.value);
                    if (value > 1) {
                        input.value = value - 1;
                    }
                });
                
                plusBtn.addEventListener('click', function() {
                    const value = parseInt(input.value);
                    if (value < 99) {
                        input.value = value + 1;
                    }
                });
            }
        });
    </script>
</form>