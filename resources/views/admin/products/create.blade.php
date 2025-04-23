@extends('layouts.admin')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Nuevo Producto</h2>
                <a href="{{ route('admin.products.index') }}" class="bg-gray-500 text-white py-2 px-4 rounded hover:bg-gray-600">
                    Volver
                </a>
            </div>

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Información básica -->
                    <div class="col-span-2">
                        <h3 class="text-lg font-bold mb-2 pb-2 border-b">Información Básica</h3>
                    </div>

                    <div class="mb-4">
                        <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Nombre:</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>

                    <div class="mb-4">
                        <label for="sku" class="block text-gray-700 text-sm font-bold mb-2">SKU:</label>
                        <input type="text" name="sku" id="sku" value="{{ old('sku') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>

                    <div class="col-span-2 mb-4">
                        <label for="short_description" class="block text-gray-700 text-sm font-bold mb-2">Descripción Corta:</label>
                        <textarea name="short_description" id="short_description" rows="2" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('short_description') }}</textarea>
                    </div>

                    <div class="col-span-2 mb-4">
                        <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Descripción Completa:</label>
                        <textarea name="description" id="description" rows="6" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('description') }}</textarea>
                    </div>

                    <!-- Categoría y Marca -->
                    <div class="mb-4">
                        <label for="category_id" class="block text-gray-700 text-sm font-bold mb-2">Categoría:</label>
                        <select name="category_id" id="category_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                            <option value="">Seleccionar categoría</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="brand_id" class="block text-gray-700 text-sm font-bold mb-2">Marca:</label>
                        <select name="brand_id" id="brand_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="">Sin marca</option>
                            @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Precios -->
                    <div class="col-span-2">
                        <h3 class="text-lg font-bold mb-2 pb-2 border-b mt-4">Precios e Inventario</h3>
                    </div>

                    <div class="mb-4">
                        <label for="price" class="block text-gray-700 text-sm font-bold mb-2">Precio Regular ($):</label>
                        <input type="number" step="0.01" min="0" name="price" id="price" value="{{ old('price') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>

                    <div class="mb-4">
                        <label for="special_price" class="block text-gray-700 text-sm font-bold mb-2">Precio Especial ($):</label>
                        <input type="number" step="0.01" min="0" name="special_price" id="special_price" value="{{ old('special_price') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <div class="mb-4">
                        <label for="special_price_from" class="block text-gray-700 text-sm font-bold mb-2">Precio Especial Desde:</label>
                        <input type="date" name="special_price_from" id="special_price_from" value="{{ old('special_price_from') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <div class="mb-4">
                        <label for="special_price_to" class="block text-gray-700 text-sm font-bold mb-2">Precio Especial Hasta:</label>
                        <input type="date" name="special_price_to" id="special_price_to" value="{{ old('special_price_to') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <div class="mb-4">
                        <label for="quantity" class="block text-gray-700 text-sm font-bold mb-2">Cantidad en Stock:</label>
                        <input type="number" min="0" name="quantity" id="quantity" value="{{ old('quantity', 0) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>

                    <div class="mb-4">
                        <label for="weight" class="block text-gray-700 text-sm font-bold mb-2">Peso (kg):</label>
                        <input type="number" step="0.01" min="0" name="weight" id="weight" value="{{ old('weight') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <!-- Estado y opciones -->
                    <div class="col-span-2">
                        <h3 class="text-lg font-bold mb-2 pb-2 border-b mt-4">Estado y Opciones</h3>
                    </div>

                    <div class="mb-4">
                        <label for="status" class="block text-gray-700 text-sm font-bold mb-2">Estado:</label>
                        <select name="status" id="status" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Borrador</option>
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Activo</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                    </div>

                    <div class="mb-4 flex items-center mt-8">
                        <input type="checkbox" name="featured" id="featured" value="1" {{ old('featured') ? 'checked' : '' }} class="mr-2">
                        <label for="featured" class="text-gray-700 text-sm font-bold">Destacado en la tienda</label>
                    </div>

                    <!-- Imágenes -->
                    <div class="col-span-2">
                        <h3 class="text-lg font-bold mb-2 pb-2 border-b mt-4">Imágenes del Producto</h3>
                    </div>

                    <div class="col-span-2 mb-4">
                        <div id="image-container" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="border-2 border-dashed border-gray-300 p-4 rounded">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Imagen 1 (Principal):</label>
                                <input type="file" name="images[0][file]" class="w-full">
                                <input type="text" name="images[0][alt_text]" placeholder="Texto alternativo" class="mt-2 shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <input type="hidden" name="images[0][is_primary]" value="1">
                            </div>
                            <div class="border-2 border-dashed border-gray-300 p-4 rounded">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Imagen 2:</label>
                                <input type="file" name="images[1][file]" class="w-full">
                                <input type="text" name="images[1][alt_text]" placeholder="Texto alternativo" class="mt-2 shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <input type="hidden" name="images[1][is_primary]" value="0">
                            </div>
                            <div class="border-2 border-dashed border-gray-300 p-4 rounded">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Imagen 3:</label>
                                <input type="file" name="images[2][file]" class="w-full">
                                <input type="text" name="images[2][alt_text]" placeholder="Texto alternativo" class="mt-2 shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <input type="hidden" name="images[2][is_primary]" value="0">
                            </div>
                        </div>
                        <button type="button" id="add-image" class="mt-2 bg-gray-200 hover:bg-gray-300 text-gray-700 py-1 px-3 rounded text-sm">+ Añadir más imágenes</button>
                    </div>

                    <!-- Atributos -->
                    <div class="col-span-2">
                        <h3 class="text-lg font-bold mb-2 pb-2 border-b mt-4">Atributos del Producto</h3>
                    </div>

                    <div class="col-span-2 mb-4">
                        @foreach($attributes as $attribute)
                            <div class="mb-4 p-4 border rounded">
                                <h4 class="font-bold mb-2">{{ $attribute->name }} ({{ ucfirst($attribute->type) }})</h4>
                                
                                @if(in_array($attribute->type, ['select', 'radio', 'checkbox']))
                                    <select name="attributes[{{ $attribute->id }}][attribute_value_id]" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline {{ $attribute->is_required ? 'required' : '' }}">
                                        <option value="">Seleccionar {{ $attribute->name }}</option>
                                        @foreach($attribute->values as $value)
                                            <option value="{{ $value->id }}" {{ old("attributes.{$attribute->id}.attribute_value_id") == $value->id ? 'selected' : '' }}>{{ $value->value }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <input type="{{ $attribute->type == 'number' ? 'number' : 'text' }}" 
                                           name="attributes[{{ $attribute->id }}][custom_value]" 
                                           value="{{ old("attributes.{$attribute->id}.custom_value") }}" 
                                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                                           {{ $attribute->is_required ? 'required' : '' }}>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center justify-end mt-6">
                    <button type="submit" class="bg-indigo-600 text-white py-2 px-4 rounded hover:bg-indigo-700 focus:outline-none focus:shadow-outline">
                        Guardar Producto
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let imageIndex = 3; // Empezamos con 3 porque ya tenemos 0, 1 y 2
        
        document.getElementById('add-image').addEventListener('click', function() {
            const container = document.getElementById('image-container');
            const newImageDiv = document.createElement('div');
            newImageDiv.className = 'border-2 border-dashed border-gray-300 p-4 rounded';
            newImageDiv.innerHTML = `
                <label class="block text-gray-700 text-sm font-bold mb-2">Imagen ${imageIndex + 1}:</label>
                <input type="file" name="images[${imageIndex}][file]" class="w-full">
                <input type="text" name="images[${imageIndex}][alt_text]" placeholder="Texto alternativo" class="mt-2 shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <input type="hidden" name="images[${imageIndex}][is_primary]" value="0">
            `;
            container.appendChild(newImageDiv);
            imageIndex++;
        });
    });
</script>
@endsection