<div x-data="{ open: false }" @click.away="open = false" class="relative">
    <button @click="open = !open" class="flex items-center text-gray-700 focus:outline-none">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
        </svg>
        @if($cartItemCount > 0)
            <span class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">
                {{ $cartItemCount }}
            </span>
        @endif
    </button>
    
    <div x-show="open" 
        x-transition:enter="transition ease-out duration-200" 
        x-transition:enter-start="opacity-0 scale-95" 
        x-transition:enter-end="opacity-100 scale-100" 
        x-transition:leave="transition ease-in duration-150" 
        x-transition:leave-start="opacity-100 scale-100" 
        x-transition:leave-end="opacity-0 scale-95" 
        class="absolute right-0 mt-2 w-72 bg-white rounded-md shadow-lg z-50 overflow-hidden"
        style="display: none;">
        
        <div class="py-2 px-4 bg-gray-50 border-b">
            <div class="flex justify-between items-center">
                <h3 class="text-sm font-medium">Tu Carrito</h3>
                <span class="text-xs text-gray-500">{{ $cartItemCount }} item(s)</span>
            </div>
        </div>
        
        <div class="max-h-64 overflow-y-auto">
            @if($cartItemCount > 0)
                @foreach($cart->items as $item)
                    <div class="py-2 px-4 border-b last:border-b-0 hover:bg-gray-50">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0 w-10 h-10">
                                @if($item->product->images->count() > 0)
                                    <img class="w-full h-full object-cover rounded" src="{{ asset('storage/' . $item->product->images->first()->image) }}" alt="{{ $item->product->name }}">
                                @else
                                    <div class="w-full h-full bg-gray-200 rounded flex items-center justify-center">
                                        <span class="text-xs text-gray-500">No img</span>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $item->product->name }}</p>
                                <p class="text-xs text-gray-500">{{ $item->quantity }} x {{ config('app.currency_symbol') }} {{ number_format($item->price, 2) }}</p>
                            </div>
                            <div class="flex-shrink-0">
                                <form action="{{ route('cart.remove') }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="cart_item_id" value="{{ $item->id }}">
                                    <button type="submit" class="text-xs text-red-600 hover:text-red-800">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="py-8 px-4 text-center">
                    <p class="text-gray-500 text-sm">Tu carrito está vacío</p>
                </div>
            @endif
        </div>
        
        @if($cartItemCount > 0)
            <div class="py-3 px-4 bg-gray-50 border-t">
                <div class="flex justify-between mb-2">
                    <span class="text-sm text-gray-600">Subtotal:</span>
                    <span class="text-sm font-medium">{{ config('app.currency_symbol') }} {{ number_format($cart->subtotal, 2) }}</span>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('cart.index') }}" class="text-center text-sm py-2 border border-gray-300 rounded hover:bg-gray-50">
                        Ver Carrito
                    </a>
                    <a href="{{ route('checkout.index') }}" class="text-center text-sm text-white py-2 bg-indigo-600 rounded hover:bg-indigo-700">
                        Checkout
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>