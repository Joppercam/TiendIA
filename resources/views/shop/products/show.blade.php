@extends('layouts.shop')

@section('title', $product->name)

@section('content')
    <div class="container mx-auto px-4 py-8">
        <nav class="text-sm mb-6" aria-label="Breadcrumb">
            <ol class="list-none p-0 inline-flex">
                <li class="flex items-center">
                    <a href="{{ route('home') }}" class="text-gray-500 hover:text-indigo-600">Inicio</a>
                    <svg class="h-5 w-5 fill-current mx-1" viewBox="0 0 24 24">
                        <path d="M8.59,16.59L13.17,12L8.59,7.41L10,6l6,6l-6,6L8.59,16.59z"/>
                    </svg>
                </li>
                <li class="flex items-center">
                    <a href="{{ route('shop.products.category', $product->category->slug) }}" class="text-gray-500 hover:text-indigo-600">{{ $product->category->name }}</a>
                    <svg class="h-5 w-5 fill-current mx-1" viewBox="0 0 24 24">
                        <path d="M8.59,16.59L13.17,12L8.59,7.41L10,6l6,6l-6,6L8.59,16.59z"/>
                    </svg>
                </li>
                <li class="text-gray-800">{{ $product->name }}</li>
            </ol>
        </nav>

        <div class="flex flex-col md:flex-row -mx-4">
            <!-- Imágenes del producto -->
            <div class="md:w-2/5 px-4 mb-8 md:mb-0">
                <div class="sticky top-6">
                    <div class="mb-4 relative">
                        <img id="main-image" src="{{ asset('storage/products/' . ($product->primaryImage ? $product->primaryImage->image : 'placeholder.jpg')) }}" alt="{{ $product->name }}" class="w-full h-auto rounded-lg shadow-md">
                        @if($product->special_price && $product->isSpecialPriceValid())
                            <span class="absolute top-4 right-4 bg-red-500 text-white px-2 py-1 rounded-md text-sm font-semibold">
                                OFERTA
                            </span>
                        @endif
                    </div>
                    
                    @if($product->images->count() > 1)
                        <div class="grid grid-cols-4 gap-2">
                            @foreach($product->images as $image)
                                <button onclick="changeMainImage('{{ asset('storage/products/' . $image->image) }}', '{{ $image->alt_text ?? $product->name }}')" class="border-2 rounded-md overflow-hidden {{ $image->is_primary ? 'border-indigo-500' : 'border-gray-200' }} hover:border-indigo-500 transition-colors duration-200">
                                    <img src="{{ asset('storage/products/thumbnails/' . $image->image) }}" alt="{{ $image->alt_text ?? $product->name }}" class="w-full h-auto">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Información del producto -->
            <div class="md:w-3/5 px-4">
                <h1 class="text-3xl font-bold mb-2">{{ $product->name }}</h1>
                
                @if($product->brand)
                    <p class="text-gray-600 mb-4">Marca: <a href="{{ route('shop.products.brand', $product->brand->slug) }}" class="text-indigo-600 hover:text-indigo-800">{{ $product->brand->name }}</a></p>
                @endif
                
                <!-- Valoraciones (Nuevo) -->
                <div class="mb-4">
                    <div class="flex items-center">
                        <div class="flex text-yellow-400 mr-2">
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= round($product->average_rating ?? 0))
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                    </svg>
                                @endif
                            @endfor
                        </div>
                        <span class="text-lg font-bold">{{ number_format($product->average_rating ?? 0, 1) }}</span>
                        <span class="text-gray-500 ml-2">({{ $product->rating_count ?? 0 }} valoraciones)</span>
                        <a href="{{ route('products.reviews.index', $product) }}" class="ml-4 text-indigo-600 hover:text-indigo-800 text-sm">Ver reseñas</a>
                    </div>
                </div>
                
                <div class="mb-4">
                    <span class="text-gray-800 font-semibold">SKU:</span> {{ $product->sku }}
                </div>
                
                <div class="mb-6">
                    @if($product->special_price && $product->isSpecialPriceValid())
                        <span class="text-gray-500 line-through text-xl">${{ number_format($product->price, 2) }}</span>
                        <span class="ml-2 text-red-600 font-bold text-2xl">${{ number_format($product->special_price, 2) }}</span>
                        <span class="ml-2 bg-red-100 text-red-800 text-xs font-semibold px-2 py-1 rounded">
                            {{ round((($product->price - $product->special_price) / $product->price) * 100) }}% DESCUENTO
                        </span>
                    @else
                        <span class="text-gray-900 font-bold text-2xl">${{ number_format($product->price, 2) }}</span>
                    @endif
                </div>
                
                <div class="mb-6">
                    <div class="flex items-center">
                        <span class="text-gray-800 font-semibold mr-2">Disponibilidad:</span>
                        @if($product->quantity > 0)
                            <span class="bg-green-100 text-green-800 text-sm font-semibold px-2 py-1 rounded">
                                En stock ({{ $product->quantity }} disponibles)
                            </span>
                        @else
                            <span class="bg-red-100 text-red-800 text-sm font-semibold px-2 py-1 rounded">
                                Agotado
                            </span>
                        @endif
                    </div>
                </div>
                
                @if($product->short_description)
                    <div class="mb-6">
                        <p class="text-gray-700">{{ $product->short_description }}</p>
                    </div>
                @endif
                
                <!-- Atributos del producto -->
                @if($product->attributes->count() > 0)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-3">Especificaciones:</h3>
                        <div class="grid grid-cols-2 gap-4">
                            @foreach($product->attributes as $attribute)
                                <div>
                                    <span class="text-gray-800 font-semibold">{{ $attribute->name }}:</span>
                                    <span class="ml-1">
                                        @if($attribute->pivot->attribute_value_id)
                                            {{ optional($attribute->values->where('id', $attribute->pivot->attribute_value_id)->first())->value }}
                                        @else
                                            {{ $attribute->pivot->custom_value }}
                                        @endif
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                <!-- Formulario para añadir al carrito -->
                <form action="{{ route('cart.add') }}" method="POST" class="mb-8">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    
                    <div class="flex items-center mb-4">
                        <label for="quantity" class="mr-4 text-gray-800 font-semibold">Cantidad:</label>
                        <div class="flex items-center border rounded-md overflow-hidden">
                            <button type="button" id="decrease-quantity" class="px-3 py-1 bg-gray-100 hover:bg-gray-200">-</button>
                            <input type="number" id="quantity" name="quantity" value="1" min="1" max="{{ $product->quantity }}" class="w-16 text-center py-1 border-0 focus:outline-none focus:ring-0">
                            <button type="button" id="increase-quantity" class="px-3 py-1 bg-gray-100 hover:bg-gray-200">+</button>
                        </div>
                    </div>
                    
                    <div class="flex space-x-4">
                        <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-6 rounded-lg flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Añadir al Carrito
                        </button>
                        
                        <button type="button" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-800 font-semibold py-3 px-4 rounded-lg flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </button>
                    </div>
                </form>
                
                <!-- Descripción completa -->
                <div class="mb-8">
                    <h3 class="text-xl font-semibold mb-4 pb-2 border-b">Descripción del Producto</h3>
                    <div class="prose prose-indigo max-w-none">
                        {{ $product->description }}
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Reseñas destacadas (Nuevo) -->
        @if(isset($product->reviews) && $product->reviews()->approved()->featured()->count() > 0)
        <div class="mt-12 bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold mb-6">Reseñas destacadas</h2>
            
            <div class="space-y-6">
                @foreach($product->reviews()->approved()->featured()->with('user:id,name')->with('rating')->limit(2)->get() as $review)
                    <div class="border-l-4 border-indigo-500 pl-4 py-2">
                        <div class="flex items-center mb-2">
                            <div class="flex text-yellow-400 mr-2">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= $review->rating->score)
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                        </svg>
                                    @endif
                                @endfor
                            </div>
                            <h3 class="text-lg font-semibold">{{ $review->title }}</h3>
                        </div>
                        <p class="text-gray-700 mb-2">{{ Str::limit($review->comment, 200) }}</p>
                        <div class="flex items-center text-sm text-gray-500">
                            <span>{{ $review->user->name }}</span>
                            <span class="mx-2">•</span>
                            <span>{{ $review->created_at->format('d/m/Y') }}</span>
                            @if($review->is_verified_purchase)
                                <span class="ml-2 bg-green-100 text-green-800 text-xs px-2 py-0.5 rounded">Compra verificada</span>
                            @endif
                        </div>
                    </div>
                @endforeach
                
                <div class="text-center mt-4">
                    <a href="{{ route('products.reviews.index', $product) }}" class="bg-indigo-100 text-indigo-700 hover:bg-indigo-200 px-4 py-2 rounded-md inline-block font-medium">
                        Ver todas las reseñas
                    </a>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Preguntas y respuestas (Nuevo) -->
        @if(isset($product->questions) && $product->questions()->approved()->count() > 0)
        <div class="mt-12 bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold mb-6">Preguntas y respuestas</h2>
            
            <div class="space-y-6">
                @foreach($product->questions()->approved()->with('user:id,name')->with(['answers' => function($q) {
                    $q->approved()->latest();
                }])->with('answers.user:id,name')->limit(2)->get() as $question)
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold mb-1">{{ $question->question }}</p>
                                <p class="text-sm text-gray-500">{{ $question->user->name }} - {{ $question->created_at->format('d/m/Y') }}</p>
                                
                                @if($question->answers->count() > 0)
                                    <div class="mt-4 pl-4 border-l-2 border-gray-200">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0 mr-4">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                            <div class="flex-1">
                                                <p class="mb-1">{{ $question->answers->first()->answer }}</p>
                                                <p class="text-sm text-gray-500">
                                                    {{ $question->answers->first()->user->name }} - {{ $question->answers->first()->created_at->format('d/m/Y') }}
                                                    @if($question->answers->first()->is_from_seller)
                                                        <span class="ml-2 bg-blue-100 text-blue-800 text-xs px-2 py-0.5 rounded">Vendedor</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
                
                <div class="text-center mt-4">
                    <a href="{{ route('products.questions.index', $product) }}" class="bg-indigo-100 text-indigo-700 hover:bg-indigo-200 px-4 py-2 rounded-md inline-block font-medium">
                        Ver todas las preguntas
                    </a>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Formulario para hacer preguntas (Nuevo) -->
        <div class="mt-8 bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold mb-4">¿Tienes alguna pregunta?</h2>
            
            @auth
                <form action="{{ route('products.questions.store', $product) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <textarea name="question" rows="2" 
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:border-indigo-500"
                                placeholder="Escribe tu pregunta aquí..." required>{{ old('question') }}</textarea>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-md">
                            Enviar pregunta
                        </button>
                    </div>
                </form>
            @else
                <p class="text-gray-600">
                    <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-800">Inicia sesión</a> para hacer una pregunta sobre este producto.
                </p>
            @endauth
        </div>
        
        <!-- Productos relacionados -->
        @if($relatedProducts->count() > 0)
            <div class="mt-16">
                <h2 class="text-2xl font-bold mb-6">Productos Relacionados</h2>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($relatedProducts as $relatedProduct)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <a href="{{ route('shop.products.show', $relatedProduct->slug) }}">
                                <div class="h-48 overflow-hidden">
                                    @if($relatedProduct->primaryImage)
                                        <img src="{{ asset('storage/products/' . $relatedProduct->primaryImage->image) }}" alt="{{ $relatedProduct->name }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                                    @else
                                        <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                            <span class="text-gray-500">Sin imagen</span>
                                        </div>
                                    @endif
                                </div>
                            </a>
                            
                            <div class="p-4">
                                <a href="{{ route('shop.products.show', $relatedProduct->slug) }}" class="block">
                                    <h3 class="text-lg font-semibold leading-tight hover:text-indigo-600">{{ $relatedProduct->name }}</h3>
                                </a>
                                
                                <div class="mt-2">
                                    @if($relatedProduct->special_price && $relatedProduct->isSpecialPriceValid())
                                        <span class="text-gray-500 line-through">${{ number_format($relatedProduct->price, 2) }}</span>
                                        <span class="ml-1 text-red-600 font-semibold">${{ number_format($relatedProduct->special_price, 2) }}</span>
                                    @else
                                        <span class="text-gray-900 font-semibold">${{ number_format($relatedProduct->price, 2) }}</span>
                                    @endif
                                </div>
                                
                                <div class="mt-4">
                                    <form action="{{ route('cart.add') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $relatedProduct->id }}">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700 flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            Añadir
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cambiar imagen principal
        window.changeMainImage = function(src, alt) {
            const mainImage = document.getElementById('main-image');
            mainImage.src = src;
            mainImage.alt = alt;
        };
        
        // Control de cantidad
        const quantityInput = document.getElementById('quantity');
        const decreaseBtn = document.getElementById('decrease-quantity');
        const increaseBtn = document.getElementById('increase-quantity');
        
        decreaseBtn.addEventListener('click', function() {
            const currentValue = parseInt(quantityInput.value);
            if (currentValue > 1) {
                quantityInput.value = currentValue - 1;
            }
        });
        
        increaseBtn.addEventListener('click', function() {
            const currentValue = parseInt(quantityInput.value);
            const maxValue = parseInt(quantityInput.max);
            if (currentValue < maxValue) {
                quantityInput.value = currentValue + 1;
            }
        });
        
        // Sistema de valoración con estrellas (Nuevo)
        const quickRatingStars = document.querySelectorAll('.quick-rating-star');
        
        if (quickRatingStars.length > 0) {
            quickRatingStars.forEach(star => {
                star.addEventListener('click', function() {
                    const productId = this.closest('.quick-rating').dataset.productId;
                    const rating = this.dataset.rating;
                    
                    // Enviar la valoración mediante AJAX
                    fetch(`/products/${productId}/rate`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            score: rating
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Actualizar la UI
                            const stars = document.querySelectorAll('.quick-rating-star');
                            
                            stars.forEach(s => {
                                if (s.dataset.rating <= rating) {
                                    s.classList.add('text-yellow-400');
                                    s.classList.remove('text-gray-300');
                                } else {
                                    s.classList.remove('text-yellow-400');
                                    s.classList.add('text-gray-300');
                                }
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
                });
            });
        }
    });
</script>
@endsection