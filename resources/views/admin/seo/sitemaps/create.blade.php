@extends('admin.layouts.app')

@section('title', 'Crear Configuración de Sitemap')

@section('content')
<div class="container py-6">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Crear Configuración de Sitemap</h1>
                <p class="mt-1 text-gray-600">Define una nueva configuración para generar sitemaps XML.</p>
            </div>
            <div>
                <a href="{{ route('admin.seo.sitemaps.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                    Volver
                </a>
            </div>
        </div>
    </div>

    @if(session('error'))
        <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <form action="{{ route('admin.seo.sitemaps.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                            Nombre de la Configuración
                        </label>
                        <input type="text" name="name" id="name" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            value="{{ old('name') }}"
                            placeholder="Ej: Productos principales"
                            required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">
                            Tipo de Contenido
                        </label>
                        <select name="type" id="type"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="products" {{ old('type') == 'products' ? 'selected' : '' }}>Productos</option>
                            <option value="categories" {{ old('type') == 'categories' ? 'selected' : '' }}>Categorías</option>
                            <option value="brands" {{ old('type') == 'brands' ? 'selected' : '' }}>Marcas</option>
                            <option value="pages" {{ old('type') == 'pages' ? 'selected' : '' }}>Páginas</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="frequency" class="block text-sm font-medium text-gray-700 mb-1">
                            Frecuencia de Cambio
                        </label>
                        <select name="frequency" id="frequency"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="always" {{ old('frequency') == 'always' ? 'selected' : '' }}>Siempre</option>
                            <option value="hourly" {{ old('frequency') == 'hourly' ? 'selected' : '' }}>Cada hora</option>
                            <option value="daily" {{ old('frequency') == 'daily' ? 'selected' : '' }}>Diario</option>
                            <option value="weekly" {{ old('frequency') == 'weekly' ? 'selected' : '' }}>Semanal</option>
                            <option value="monthly" {{ old('frequency') == 'monthly' ? 'selected' : '' }}>Mensual</option>
                            <option value="yearly" {{ old('frequency') == 'yearly' ? 'selected' : '' }}>Anual</option>
                            <option value="never" {{ old('frequency') == 'never' ? 'selected' : '' }}>Nunca</option>
                        </select>
                        @error('frequency')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">
                            Indica la frecuencia con la que cambia este tipo de contenido.
                        </p>
                    </div>

                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">
                            Prioridad
                            <span class="text-xs text-gray-500 font-normal ml-1">(Valor entre 0.0 y 1.0)</span>
                        </label>
                        <input type="number" name="priority" id="priority"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            value="{{ old('priority', '0.5') }}"
                            step="0.1"
                            min="0"
                            max="1"
                            required>
                        @error('priority')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">
                            1.0 es la máxima prioridad y 0.0 es la mínima. La prioridad predeterminada es 0.5.
                        </p>
                    </div>

                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="is_active" id="is_active"
                                class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
                                {{ old('is_active', true) ? 'checked' : '' }}>
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="is_active" class="font-medium text-gray-700">Activo</label>
                            <p class="text-gray-500">Indica si esta configuración debe ser incluida al generar sitemaps.</p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Crear Configuración
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection