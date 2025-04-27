@props(['product', 'show_discount_badge' => false])

<div class="bg-white rounded-lg shadow-md overflow-hidden group">
    <a href="{{ route('shop.products.show', $product->slug) }}">
        <div class="h-48 overflow-hidden relative">
            @if($product->primaryImage)
                <img src="{{ asset('storage/products/' . $product->primaryImage->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
            @else
                <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            @endif
            @if($show_discount_badge && $product->special_price && $product->isSpecialPriceValid())
                 <span class="absolute top-2 right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">OFERTA</span>
            @endif
             {{-- Puedes añadir un badge "Nuevo" similar si quieres --}}
             {{-- @if($product->created_at->gt(now()->subDays(7)))
                <span class="absolute top-2 left-2 bg-blue-500 text-white text-xs font-bold px-2 py-1 rounded">NUEVO</span>
             @endif --}}
        </div>
    </a>

    <div class="p-4">
        <a href="{{ route('shop.products.category', $product->category->slug) }}" class="text-xs text-blue-600 uppercase font-semibold tracking-wide hover:text-blue-800"> {{-- Color Azul Eléctrico --}}
            {{ $product->category->name }}
        </a>
        <a href="{{ route('shop.products.show', $product->slug) }}" class="block mt-1">
            <h3 class="text-lg font-semibold leading-tight hover:text-blue-700 h-14 overflow-hidden">{{ $product->name }}</h3> {{-- Altura fija para alinear tarjetas --}}
        </a>

        <div class="mt-2 flex items-center">
             {{-- Valoraciones (Estrellas) - Placeholder --}}
            <div class="flex text-yellow-400 text-sm mr-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                {{-- Repite según la valoración media --}}
            </div>
            <span class="text-xs text-gray-500">(0 reseñas)</span> {{-- Reemplazar con datos reales si los tienes --}}
        </div>


        <div class="mt-2">
            @if($product->special_price && $product->isSpecialPriceValid())
                <span class="text-gray-500 line-through">${{ number_format($product->price, 0, ',', '.') }}</span>
                <span class="ml-1 text-red-600 font-semibold text-lg">${{ number_format($product->special_price, 0, ',', '.') }}</span>
            @else
                <span class="text-gray-900 font-semibold text-lg">${{ number_format($product->price, 0, ',', '.') }}</span>
            @endif
        </div>

        <div class="mt-4">
            <form action="{{ route('cart.add') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 flex items-center justify-center text-sm font-medium transition-colors"> {{-- Color Azul Eléctrico --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Añadir
                </button>
            </form>
        </div>
    </div>
</div>