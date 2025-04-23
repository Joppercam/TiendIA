<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mi Lista de Deseos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if($wishList->count() > 0)
                        <div class="mb-4">
                            <h3 class="text-lg font-medium">{{ $wishList->count() }} producto(s) en tu lista de deseos</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($wishList as $item)
                                <div class="border rounded-lg overflow-hidden flex flex-col">
                                    <div class="h-48 overflow-hidden">
                                        @if($item->product->images->count() > 0)
                                            <img class="w-full h-full object-cover" src="{{ asset('storage/' . $item->product->images->first()->image) }}" alt="{{ $item->product->name }}">
                                        @else
                                            <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                                <span class="text-gray-500">No hay imagen disponible</span>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="p-4 flex-grow">
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $item->product->name }}</h3>
                                        <p class="text-gray-500 text-sm mb-4">{{ Str::limit($item->product->short_description, 100) }}</p>
                                        
                                        <div class="flex justify-between items-center mb-4">
                                            <span class="text-xl font-bold text-gray-900">
                                                {{ config('app.currency_symbol') }} {{ number_format($item->product->special_price ?? $item->product->price, 2) }}
                                            </span>
                                            
                                            @if($item->product->special_price && $item->product->special_price < $item->product->price)
                                                <span class="text-sm line-through text-gray-500">
                                                    {{ config('app.currency_symbol') }} {{ number_format($item->product->price, 2) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="p-4 bg-gray-50 border-t">
                                        <div class="flex space-x-2">
                                            <form action="{{ route('wishlist.move-to-cart') }}" method="POST" class="flex-grow">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                                                <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded hover:bg-indigo-700">
                                                    Añadir al Carrito
                                                </button>
                                            </form>
                                            
                                            <form action="{{ route('wishlist.remove') }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                                                <button type="submit" class="bg-white text-red-600 border border-red-600 py-2 px-4 rounded hover:bg-red-50">
                                                    Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-10">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-lg font-medium text-gray-900">Tu lista de deseos está vacía</h3>
                            <p class="mt-1 text-gray-500">Guarda tus productos favoritos para comprarlos más tarde.</p>
                            <div class="mt-6">
                                <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Explorar Productos
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>