@extends('admin.layouts.app')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Productos</h2>
                <a href="{{ route('admin.products.create') }}" class="bg-indigo-600 text-white py-2 px-4 rounded hover:bg-indigo-700">
                    Nuevo Producto
                </a>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-3 px-6 text-left bg-gray-100">ID</th>
                        <th class="py-3 px-6 text-left bg-gray-100">Imagen</th>
                        <th class="py-3 px-6 text-left bg-gray-100">Nombre</th>
                        <th class="py-3 px-6 text-left bg-gray-100">SKU</th>
                        <th class="py-3 px-6 text-left bg-gray-100">Precio</th>
                        <th class="py-3 px-6 text-left bg-gray-100">Categoría</th>
                        <th class="py-3 px-6 text-left bg-gray-100">Estado</th>
                        <th class="py-3 px-6 text-left bg-gray-100">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                        <tr class="hover:bg-gray-50">
                            <td class="py-4 px-6 border-b border-gray-200">{{ $product->id }}</td>
                            <td class="py-4 px-6 border-b border-gray-200">
                                @if($product->primaryImage)
                                    <img src="{{ asset('storage/products/thumbnails/' . $product->primaryImage->image) }}" alt="{{ $product->name }}" class="h-10 w-10 object-cover rounded">
                                @else
                                    <div class="h-10 w-10 bg-gray-200 rounded flex items-center justify-center text-gray-500">N/A</div>
                                @endif
                            </td>
                            <td class="py-4 px-6 border-b border-gray-200">{{ $product->name }}</td>
                            <td class="py-4 px-6 border-b border-gray-200">{{ $product->sku }}</td>
                            <td class="py-4 px-6 border-b border-gray-200">
                                @if($product->special_price && $product->isSpecialPriceValid())
                                    <span class="line-through text-gray-500">${{ number_format($product->price, 2) }}</span>
                                    <span class="text-red-600">${{ number_format($product->special_price, 2) }}</span>
                                @else
                                    ${{ number_format($product->price, 2) }}
                                @endif
                            </td>
                            <td class="py-4 px-6 border-b border-gray-200">{{ $product->category->name }}</td>
                            <td class="py-4 px-6 border-b border-gray-200">
                                <span class="px-2 py-1 rounded text-xs 
                                    @if($product->status == 'active') bg-green-100 text-green-800
                                    @elseif($product->status == 'inactive') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800
                                    @endif">
                                    {{ ucfirst($product->status) }}
                                </span>
                            </td>
                            <td class="py-4 px-6 border-b border-gray-200 flex space-x-2">
                                <a href="{{ route('admin.products.edit', $product) }}" class="text-indigo-600 hover:text-indigo-900">Editar</a>
                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('¿Estás seguro de eliminar este producto?')">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4">
                {{ $products->links() }}
            </div>
        </div>
    </div>
@endsection