@extends('layouts.shop')

@section('title', 'Tienda en línea')

@section('content')
    <!-- Hero Banner -->
    <div class="bg-indigo-700 text-white py-16">
        <div class="container mx-auto px-4 flex flex-col md:flex-row items-center">
            <div class="md:w-1/2 mb-10 md:mb-0">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">Bienvenido a TiendIA</h1>
                <p class="text-xl mb-6">Tu tienda online de confianza con los mejores productos al mejor precio.</p>
                <a href="{{ route('shop.products.index') }}" class="bg-white text-indigo-700 font-semibold py-3 px-8 rounded-lg hover:bg-indigo-100 transition-colors">
                    Ver Productos
                </a>
            </div>
            <div class="md:w-1/2 flex justify-center">
                <img src="{{ asset('img/hero-banner.jpg') }}" alt="TiendIA" class="rounded-lg shadow-2xl max-w-full h-auto">
            </div>
        </div>
    </div>
    
    <!-- Categorías Destacadas -->
    <div class="container mx-auto px-4 py-16">
        <h2 class="text-3xl font-bold mb-8 text-center">Categorías Destacadas</h2>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            @foreach($categories as $category)
                <a href="{{ route('shop.products.category', $category->slug) }}" class="group">
                    <div class="bg-gray-100 rounded-lg overflow-hidden shadow-md hover:shadow-xl transition-shadow">
                        <div class="h-40 overflow-hidden">
                            @if($category->image)
                                <img src="{{ asset('storage/categories/' . $category->image) }}" alt="{{ $category->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full bg-indigo-200 flex items-center justify-center text-indigo-600">
                                    <span class="text-2xl font-bold">{{ substr($category->name, 0, 1) }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="p-4 text-center">
                            <h3 class="font-semibold text-lg group-hover:text-indigo-600 transition-colors">{{ $category->name }}</h3>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    
    <!-- Productos Destacados -->
    @if($featuredProducts->count() > 0)
        <div class="bg-gray-100 py-16">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-bold mb-8 text-center">Productos Destacados</h2>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($featuredProducts as $product)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <a href="{{ route('shop.products.show', $product->slug) }}">
                                <div class="h-48 overflow-hidden">
                                    @if($product->primaryImage)
                                        <img src="{{ asset('storage/products/' . $product->primaryImage->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                                    @else
                                        <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                            <span class="text-gray-500">Sin imagen</span>
                                        </div>
                                    @endif
                                </div>
                            </a>
                            
                            <div class="p-4">
                                <a href="{{ route('shop.products.category', $product->category->slug) }}" class="text-xs text-indigo-600 uppercase font-semibold tracking-wide">
                                    {{ $product->category->name }}
                                </a>
                                <a href="{{ route('shop.products.show', $product->slug) }}" class="block mt-1">
                                    <h3 class="text-lg font-semibold leading-tight hover:text-indigo-600">{{ $product->name }}</h3>
                                </a>
                                
                                <div class="mt-2">
                                    @if($product->special_price && $product->isSpecialPriceValid())
                                        <span class="text-gray-500 line-through">${{ number_format($product->price, 2) }}</span>
                                        <span class="ml-1 text-red-600 font-semibold">${{ number_format($product->special_price, 2) }}</span>
                                    @else
                                        <span class="text-gray-900 font-semibold">${{ number_format($product->price, 2) }}</span>
                                    @endif
                                </div>
                                
                                <div class="mt-4 flex justify-between items-center">
                                    <a href="{{ route('shop.products.show', $product->slug) }}" class="text-indigo-600 hover:text-indigo-800">
                                        Ver detalles
                                    </a>
                                    
                                    <form action="{{ route('cart.add') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="bg-indigo-600 text-white p-2 rounded-full hover:bg-indigo-700">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-8 text-center">
                    <a href="{{ route('shop.products.index') }}" class="bg-indigo-600 text-white py-3 px-8 rounded-lg hover:bg-indigo-700 transition-colors">
                        Ver Todos los Productos
                    </a>
                </div>
            </div>
        </div>
    @endif
    
    <!-- Productos Nuevos -->
    @if($newProducts->count() > 0)
        <div class="container mx-auto px-4 py-16">
            <h2 class="text-3xl font-bold mb-8 text-center">Nuevas Llegadas</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($newProducts as $product)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <a href="{{ route('shop.products.show', $product->slug) }}">
                            <div class="h-48 overflow-hidden">
                                @if($product->primaryImage)
                                    <img src="{{ asset('storage/products/' . $product->primaryImage->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                        <span class="text-gray-500">Sin imagen</span>
                                    </div>
                                @endif
                            </div>
                        </a>
                        
                        <div class="p-4">
                            <a href="{{ route('shop.products.category', $product->category->slug) }}" class="text-xs text-indigo-600 uppercase font-semibold tracking-wide">
                                {{ $product->category->name }}
                            </a>
                            <a href="{{ route('shop.products.show', $product->slug) }}" class="block mt-1">
                                <h3 class="text-lg font-semibold leading-tight hover:text-indigo-600">{{ $product->name }}</h3>
                            </a>
                            
                            <div class="mt-2">
                                @if($product->special_price && $product->isSpecialPriceValid())
                                    <span class="text-gray-500 line-through">${{ number_format($product->price, 2) }}</span>
                                    <span class="ml-1 text-red-600 font-semibold">${{ number_format($product->special_price, 2) }}</span>
                                @else
                                    <span class="text-gray-900 font-semibold">${{ number_format($product->price, 2) }}</span>
                                @endif
                            </div>
                            
                            <div class="mt-4 flex justify-between items-center">
                                <a href="{{ route('shop.products.show', $product->slug) }}" class="text-indigo-600 hover:text-indigo-800">
                                    Ver detalles
                                </a>
                                
                                <form action="{{ route('cart.add') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="bg-indigo-600 text-white p-2 rounded-full hover:bg-indigo-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
@endsection