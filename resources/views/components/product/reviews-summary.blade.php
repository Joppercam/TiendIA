@props(['product'])

<div class="bg-white rounded-lg shadow p-4 mb-4">
    <div class="flex items-center mb-2">
        <div class="flex text-yellow-400 mr-2">
            @for ($i = 1; $i <= 5; $i++)
                @if ($i <= round($product->average_rating))
                    <i class="fas fa-star"></i>
                @else
                    <i class="far fa-star"></i>
                @endif
            @endfor
        </div>
        <span class="text-lg font-bold">{{ number_format($product->average_rating, 1) }}</span>
        <span class="text-gray-500 ml-2">({{ $product->rating_count }} valoraciones)</span>
    </div>
    
    <div class="flex justify-between">
        <a href="{{ route('products.reviews.index', $product) }}" class="text-blue-600 hover:text-blue-800">
            Ver {{ $product->review_count }} reseñas
        </a>
        
        <a href="{{ route('products.questions.index', $product) }}" class="text-blue-600 hover:text-blue-800">
            Ver {{ $product->questions_count }} preguntas
        </a>
    </div>
    
    @auth
        <div class="mt-3 flex justify-between items-center">
            <div class="text-sm">¿Has comprado este producto?</div>
            <a href="{{ route('products.reviews.create', $product) }}" 
               class="bg-blue-600 text-white px-3 py-1 text-sm rounded hover:bg-blue-700">
                Escribir una reseña
            </a>
        </div>
    @endauth
</div>