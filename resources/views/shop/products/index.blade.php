@extends('layouts.shop')

@section('title', 'Productos')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row">
            <!-- Filtros -->
            <div class="w-full md:w-1/4 px-4 mb-8">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-bold mb-4">Filtros</h3>
                    
                    <form action="{{ route('shop.products.index') }}" method="GET">
                        @if(request('q'))
                            <input type="hidden" name="q" value="{{ request('q') }}">
                        @endif
                        
                        <!-- Categorías -->
                        <div class="mb-6">
                            <h4 class="font-semibold mb-2">Categorías</h4>
                            <div class="max-h-48 overflow-y-auto">
                                @foreach($filters['categories'] as $category)
                                    <div class="flex items-center mb-2">
                                        <input type="radio" id="category-{{ $category->id }}" name="category" value="{{ $category->id }}" 
                                        {{ request('category') == $category->id ? 'checked' : '' }} class="mr-2">
                                        <label for="category-{{ $category->id }}">{{ $category->name }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- Marcas -->
                        <div class="mb-6">
                            <h4 class="font-semibold mb-2">Marcas</h4>
                            <div class="max-h-48 overflow-y-auto">
                                @foreach($filters['brands'] as $brand)
                                    <div class="flex items-center mb-2">
                                        <input type="checkbox" id="brand-{{ $brand->id }}" name="brand" value="{{ $brand->id }}" 
                                        {{ request('brand') == $brand->id ? 'checked' : '' }} class="mr-2">
                                        <label for="brand-{{ $brand->id }}">{{ $brand->name }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- Precio -->
                        <div class="mb-6">
                            <h4 class="font-semibold mb-2">Precio</h4>
                            <div class="flex items-center">
                                <input type="number" name="price_min" value="{{ $priceMin ?? $filters['price']['min'] }}" min="{{ $filters['price']['min'] }}" placeholder="Min" class="w-1/2 border rounded p-2 mr-2">
                                <input type="number" name="price_max" value="{{ $priceMax ?? $filters['price']['max'] }}" max="{{ $filters['price']['max'] }}" placeholder="Max" class="w-1/2 border rounded p-2">
                            </div>
                        </div>
                        
                        <!-- Atributos -->
                        @if(isset($filters['attributes']))
                            @foreach($filters['attributes'] as $attribute)
                                <div class="mb-6">
                                    <h4 class="font-semibold mb-2">{{ $attribute->name }}</h4>
                                    <div class="max-h-48 overflow-y-auto">
                                        @foreach($attribute->values as $value)
                                            <div class="flex items-center mb-2">
                                                <input type="checkbox" id="attr-{{ $attribute->id }}-{{ $value->id }}" 
                                                       name="attributes[{{ $attribute->id }}][]" 
                                                       value="{{ $value->id }}"
                                                       {{ isset($attributes[$attribute->id]) && in_array($value->id, (array)$attributes[$attribute->id]) ? 'checked' : '' }}
                                                       class="mr-2">
                                                <label for="attr-{{ $attribute->id }}-{{ $value->id }}">{{ $value->value }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        @endif
                        
                        <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded hover:bg-indigo-700">
                            Aplicar Filtros
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Productos -->
            <div class="w-full md:w-3/4 px-4">
                <!-- Encabezado y ordenamiento -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold">
                            @if($searchQuery)
                                Resultados para "{{ $searchQuery }}"
                            @elseif($currentCategory)
                                {{ $currentCategory->name }}
                            @else
                                Todos los Productos
                            @endif
                        </h2>
                        <p class="text-gray-600">{{ $products->total() }} productos encontrados</p>
                    </div>
                    
                    <div class="mt-4 md:mt-0">
                        <label for="sort" class="mr-2">Ordenar por:</label>
                        <select id="sort" onchange="this.form.submit()" name="sort_by" form="sort-form" class="border rounded p-2">
                            <option value="newest" {{ $sortBy == 'newest' ? 'selected' : '' }}>Más recientes</option>
                            <option value="price_asc" {{ $sortBy == 'price_asc' ? 'selected' : '' }}>Precio: menor a mayor</option>
                            <option value="price_desc" {{ $sortBy == 'price_desc' ? 'selected' : '' }}>Precio: mayor a menor</option>
                            <option value="name_asc" {{ $sortBy == 'name_asc' ? 'selected' : '' }}>Nombre: A-Z</option>
                            <option value="name_desc" {{ $sortBy == 'name_desc' ? 'selected' : '' }}>Nombre: Z-A</option>
                        </select>
                        <form id="sort-form" action="{{ route('shop.products.index') }}" method="GET">
                            @if(request('q'))
                                <input type="hidden" name="q" value="{{ request('q') }}">
                            @endif
                            @if(request('category'))
                                <input type="hidden" name="category" value="{{ request('category') }}">
                            @endif
                            @if(request('brand'))
                                <input type="hidden" name="brand" value="{{ request('brand') }}">
                            @endif
                            @if(request('price_min'))
                                <input type="hidden" name="price_min" value="{{ request('price_min') }}">
                            @endif
                            @if(request('price_max'))
                                <input type="hidden" name="price_max" value="{{ request('price_max') }}">
                            @endif
                            <!-- Atributos no incluidos por simplicidad -->
                        </form>
                    </div>
                </div>
                
                <!-- Lista de productos -->
                @if($products->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($products as $product)
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
                    
                    <div class="mt-8">
                        {{ $products->links() }}
                    </div>
                @else
                    <div class="bg-gray-100 p-8 rounded-lg text-center">
                        <h3 class="text-xl font-semibold mb-2">No se encontraron productos</h3>
                        <p class="text-gray-600 mb-4">Intenta con otros criterios de búsqueda o filtros.</p>
                        <a href="{{ route('shop.products.index') }}" class="bg-indigo-600 text-white py-2 px-4 rounded hover:bg-indigo-700">
                            Ver todos los productos
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection