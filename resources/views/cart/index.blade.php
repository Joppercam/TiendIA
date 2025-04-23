<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tu Carrito de Compras') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if($cart->items->count() > 0)
                        <div class="mb-4 flex justify-between items-center">
                            <h3 class="text-lg font-medium">{{ $cart->items->count() }} producto(s) en tu carrito</h3>
                            <form action="{{ route('cart.clear') }}" method="POST">
                                @csrf
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    Vaciar carrito
                                </button>
                            </form>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Producto
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Precio
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Cantidad
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Subtotal
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($cart->items as $item)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        @if($item->product->images->count() > 0)
                                                            <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $item->product->images->first()->image) }}" alt="{{ $item->product->name }}">
                                                        @else
                                                            <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                                <span class="text-xs">No img</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ $item->product->name }}
                                                        </div>
                                                        <div class="text-sm text-gray-500">
                                                            SKU: {{ $item->product->sku }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ config('app.currency_symbol') }} {{ number_format($item->price, 2) }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <form action="{{ route('cart.update') }}" method="POST" class="flex items-center">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="cart_item_id" value="{{ $item->id }}">
                                                    <button type="button" class="quantity-btn minus text-gray-500 focus:outline-none" data-input="quantity-{{ $item->id }}">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                                        </svg>
                                                    </button>
                                                    <input id="quantity-{{ $item->id }}" name="quantity" type="number" min="1" max="99" value="{{ $item->quantity }}" class="mx-2 w-14 text-center border-gray-200 rounded">
                                                    <button type="button" class="quantity-btn plus text-gray-500 focus:outline-none" data-input="quantity-{{ $item->id }}">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                        </svg>
                                                    </button>
                                                    <button type="submit" class="ml-2 text-indigo-600 hover:text-indigo-800">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ config('app.currency_symbol') }} {{ number_format($item->subtotal, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <form action="{{ route('cart.remove') }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" name="cart_item_id" value="{{ $item->id }}">
                                                        <button type="submit" class="text-red-600 hover:text-red-800">
                                                            Eliminar
                                                        </button>
                                                    </form>
                                                    
                                                    @auth
                                                        <form action="{{ route('cart.save-for-later') }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                                                            <input type="hidden" name="cart_item_id" value="{{ $item->id }}">
                                                            <button type="submit" class="text-gray-600 hover:text-gray-800">
                                                                Guardar para después
                                                            </button>
                                                        </form>
                                                        @else
                                            <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-800">
                                                Guardar para después
                                            </a>
                                        @endauth
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-8 bg-gray-50 p-6 rounded-lg">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium">Resumen del Carrito</h3>
                </div>

                <div class="border-t border-gray-200 pt-4">
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">Subtotal:</span>
                        <span class="font-medium">{{ config('app.currency_symbol') }} {{ number_format($cart->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">Impuestos (estimados):</span>
                        <span class="font-medium">{{ config('app.currency_symbol') }} {{ number_format($cart->subtotal * 0.16, 2) }}</span>
                    </div>
                    <div class="flex justify-between border-t border-gray-200 pt-2 mt-2">
                        <span class="text-lg font-bold">Total:</span>
                        <span class="text-lg font-bold">{{ config('app.currency_symbol') }} {{ number_format($cart->subtotal * 1.16, 2) }}</span>
                    </div>
                </div>

                <div class="mt-6">
                    <a href="{{ route('checkout.index') }}" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 flex items-center justify-center">
                        Proceder al Checkout
                        <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>
        @else
            <div class="text-center py-10">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                <h3 class="mt-2 text-lg font-medium text-gray-900">Tu carrito está vacío</h3>
                <p class="mt-1 text-gray-500">¡Explora nuestro catálogo y añade productos a tu carrito!</p>
                <div class="mt-6">
                    <a href="{{ route('shop.products.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Ver Productos
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Sección de productos recomendados -->
@if($cart->items->count() > 0)
    <div class="mt-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <h2 class="text-xl font-semibold mb-4">También podría interesarte</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Aquí se mostrarían productos recomendados -->
        </div>
    </div>
@endif
</div>
</x-app-layout>

<script>
    // Script para los botones de cantidad
    document.addEventListener('DOMContentLoaded', function() {
        const minusButtons = document.querySelectorAll('.quantity-btn.minus');
        const plusButtons = document.querySelectorAll('.quantity-btn.plus');
        
        minusButtons.forEach(button => {
            button.addEventListener('click', function() {
                const input = document.getElementById(this.dataset.input);
                const value = parseInt(input.value);
                if (value > 1) {
                    input.value = value - 1;
                }
            });
        });
        
        plusButtons.forEach(button => {
            button.addEventListener('click', function() {
                const input = document.getElementById(this.dataset.input);
                const value = parseInt(input.value);
                if (value < 99) {
                    input.value = value + 1;
                }
            });
        });
    });
</script>