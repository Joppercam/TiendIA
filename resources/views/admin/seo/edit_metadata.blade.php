@extends('admin.layouts.app')

@section('title', 'Editar Metadatos SEO')

@section('content')
<div class="container py-6">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Editar Metadatos SEO</h1>
                <p class="mt-1 text-gray-600">
                    @if($type == 'products')
                        Producto: {{ $model->name }}
                    @elseif($type == 'categories')
                        Categoría: {{ $model->name }}
                    @elseif($type == 'brands')
                        Marca: {{ $model->name }}
                    @elseif($type == 'pages')
                        Página: {{ $model->title }}
                    @endif
                </p>
            </div>
            <div>
                <a href="{{ url()->previous() }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                    Volver
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <form action="{{ route('admin.seo.metadata.update', ['type' => $type, 'id' => $model->id]) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Metadatos Básicos</h2>
                    
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                                Título SEO
                                <span class="text-xs text-gray-500 font-normal ml-1">(Recomendado: máximo 70 caracteres)</span>
                            </label>
                            <input type="text" name="title" id="title" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                value="{{ old('title', $model->seoMetadata->title ?? '') }}"
                                maxlength="70">
                            <div class="mt-1 text-xs text-gray-500">
                                <span id="title-counter">0</span>/70 caracteres
                            </div>
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-1">
                                Meta Descripción
                                <span class="text-xs text-gray-500 font-normal ml-1">(Recomendado: 120-160 caracteres)</span>
                            </label>
                            <textarea name="meta_description" id="meta_description" rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                maxlength="160">{{ old('meta_description', $model->seoMetadata->meta_description ?? '') }}</textarea>
                            <div class="mt-1 text-xs text-gray-500">
                                <span id="description-counter">0</span>/160 caracteres
                            </div>
                            @error('meta_description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="meta_keywords" class="block text-sm font-medium text-gray-700 mb-1">
                                Palabras Clave
                                <span class="text-xs text-gray-500 font-normal ml-1">(Separadas por comas)</span>
                            </label>
                            <input type="text" name="meta_keywords" id="meta_keywords"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                value="{{ old('meta_keywords', $model->seoMetadata->meta_keywords ?? '') }}"
                                maxlength="255">
                            @error('meta_keywords')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Open Graph (Facebook)</h2>
                    
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="og_title" class="block text-sm font-medium text-gray-700 mb-1">
                                Título Open Graph
                            </label>
                            <input type="text" name="og_title" id="og_title"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                value="{{ old('og_title', $model->seoMetadata->og_title ?? '') }}"
                                maxlength="70">
                            @error('og_title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="og_description" class="block text-sm font-medium text-gray-700 mb-1">
                                Descripción Open Graph
                            </label>
                            <textarea name="og_description" id="og_description" rows="2"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                maxlength="200">{{ old('og_description', $model->seoMetadata->og_description ?? '') }}</textarea>
                            @error('og_description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="og_image" class="block text-sm font-medium text-gray-700 mb-1">
                                URL de Imagen Open Graph
                                <span class="text-xs text-gray-500 font-normal ml-1">(Recomendado: 1200x630px)</span>
                            </label>
                            <input type="text" name="og_image" id="og_image"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                value="{{ old('og_image', $model->seoMetadata->og_image ?? '') }}"
                                maxlength="255">
                            @error('og_image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Twitter Card</h2>
                    
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="twitter_title" class="block text-sm font-medium text-gray-700 mb-1">
                                Título Twitter
                            </label>
                            <input type="text" name="twitter_title" id="twitter_title"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                value="{{ old('twitter_title', $model->seoMetadata->twitter_title ?? '') }}"
                                maxlength="70">
                            @error('twitter_title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="twitter_description" class="block text-sm font-medium text-gray-700 mb-1">
                                Descripción Twitter
                            </label>
                            <textarea name="twitter_description" id="twitter_description" rows="2"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                maxlength="200">{{ old('twitter_description', $model->seoMetadata->twitter_description ?? '') }}</textarea>
                            @error('twitter_description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="twitter_image" class="block text-sm font-medium text-gray-700 mb-1">
                                URL de Imagen Twitter
                                <span class="text-xs text-gray-500 font-normal ml-1">(Recomendado: 1200x630px)</span>
                            </label>
                            <input type="text" name="twitter_image" id="twitter_image"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                value="{{ old('twitter_image', $model->seoMetadata->twitter_image ?? '') }}"
                                maxlength="255">
                            @error('twitter_image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Configuración Avanzada</h2>
                    
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="canonical_url" class="block text-sm font-medium text-gray-700 mb-1">
                                URL Canónica
                                <span class="text-xs text-gray-500 font-normal ml-1">(URL completa)</span>
                            </label>
                            <input type="url" name="canonical_url" id="canonical_url"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                value="{{ old('canonical_url', $model->seoMetadata->canonical_url ?? '') }}"
                                placeholder="https://www.example.com/pagina"
                                maxlength="255">
                            @error('canonical_url')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="no_index" id="no_index"
                                    class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
                                    {{ old('no_index', $model->seoMetadata->no_index ?? false) ? 'checked' : '' }}>
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="no_index" class="font-medium text-gray-700">No indexar</label>
                                <p class="text-gray-500">Evita que esta página sea indexada por los motores de búsqueda.</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="no_follow" id="no_follow"
                                    class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
                                    {{ old('no_follow', $model->seoMetadata->no_follow ?? false) ? 'checked' : '' }}>
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="no_follow" class="font-medium text-gray-700">No seguir enlaces</label>
                                <p class="text-gray-500">Indica a los motores de búsqueda que no sigan los enlaces de esta página.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Contadores de caracteres
        const titleInput = document.getElementById('title');
        const titleCounter = document.getElementById('title-counter');
        const descriptionInput = document.getElementById('meta_description');
        const descriptionCounter = document.getElementById('description-counter');
        
        // Actualizar contadores iniciales
        titleCounter.textContent = titleInput.value.length;
        descriptionCounter.textContent = descriptionInput.value.length;
        
        // Evento para título
        titleInput.addEventListener('input', function() {
            titleCounter.textContent = this.value.length;
            
            // Opcional: Cambiar color si excede la longitud recomendada
            if (this.value.length > 60) {
                titleCounter.classList.add('text-yellow-600');
            } else {
                titleCounter.classList.remove('text-yellow-600');
            }
        });
        
        // Evento para descripción
        descriptionInput.addEventListener('input', function() {
            descriptionCounter.textContent = this.value.length;
            
            // Opcional: Cambiar color según longitud recomendada
            if (this.value.length < 120) {
                descriptionCounter.classList.add('text-yellow-600');
                descriptionCounter.classList.remove('text-green-600', 'text-red-600');
            } else if (this.value.length > 155) {
                descriptionCounter.classList.add('text-red-600');
                descriptionCounter.classList.remove('text-green-600', 'text-yellow-600');
            } else {
                descriptionCounter.classList.add('text-green-600');
                descriptionCounter.classList.remove('text-yellow-600', 'text-red-600');
            }
        });
        
        // Disparar eventos para inicializar colores
        titleInput.dispatchEvent(new Event('input'));
        descriptionInput.dispatchEvent(new Event('input'));
    });
</script>
@endsection