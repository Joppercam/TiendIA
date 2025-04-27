@extends('admin.layouts.app')

@section('title', 'Panel SEO y Optimización')

@section('content')
<div class="container py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Panel SEO y Optimización</h1>
        <p class="mt-1 text-gray-600">Gestiona las configuraciones SEO y optimiza tu sitio web.</p>
    </div>

    <!-- Tarjetas de estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-gray-900 text-lg font-semibold">Metadatos SEO</h2>
                    <p class="mt-1 text-sm text-gray-600">Gestiona títulos, descripciones y palabras clave.</p>
                </div>
            </div>
            <div class="mt-4">
                <a href="#metadata-section" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Ver sección &rarr;
                </a>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-gray-900 text-lg font-semibold">Sitemaps</h2>
                    <p class="mt-1 text-sm text-gray-600">Genera y gestiona sitemaps XML.</p>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.seo.sitemaps.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Gestionar sitemaps &rarr;
                </a>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-gray-900 text-lg font-semibold">Redirecciones</h2>
                    <p class="mt-1 text-sm text-gray-600">Gestiona redirecciones 301/302.</p>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.seo.redirects.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Gestionar redirecciones &rarr;
                </a>
            </div>
        </div>
    </div>

    <!-- Sección de metadatos -->
    <div id="metadata-section" class="bg-white p-6 rounded-lg shadow-md mb-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Metadatos SEO</h2>
        <p class="mb-4 text-gray-600">Selecciona el tipo de contenido para gestionar sus metadatos SEO:</p>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
            <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                <h3 class="font-medium text-gray-800">Productos</h3>
                <p class="text-sm text-gray-600 mt-1">Gestiona SEO de productos individuales.</p>
                <a href="{{ route('admin.products.index', ['view' => 'seo']) }}" class="mt-3 inline-block text-blue-600 hover:text-blue-800 text-sm">
                    Ver productos &rarr;
                </a>
            </div>

            <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                <h3 class="font-medium text-gray-800">Categorías</h3>
                <p class="text-sm text-gray-600 mt-1">Gestiona SEO de categorías de productos.</p>
                <a href="{{ route('admin.categories.index', ['view' => 'seo']) }}" class="mt-3 inline-block text-blue-600 hover:text-blue-800 text-sm">
                    Ver categorías &rarr;
                </a>
            </div>

            <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                <h3 class="font-medium text-gray-800">Marcas</h3>
                <p class="text-sm text-gray-600 mt-1">Gestiona SEO de marcas.</p>
                <a href="{{ route('admin.brands.index', ['view' => 'seo']) }}" class="mt-3 inline-block text-blue-600 hover:text-blue-800 text-sm">
                    Ver marcas &rarr;
                </a>
            </div>

            <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                <h3 class="font-medium text-gray-800">Páginas</h3>
                <p class="text-sm text-gray-600 mt-1">Gestiona SEO de páginas estáticas.</p>
                <a href="{{ route('admin.pages.index', ['view' => 'seo']) }}" class="mt-3 inline-block text-blue-600 hover:text-blue-800 text-sm">
                    Ver páginas &rarr;
                </a>
            </div>
        </div>
    </div>

    <!-- Sección de herramientas -->
    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Herramientas SEO</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-6">
            <div class="border rounded-lg p-4">
                <h3 class="font-medium text-gray-800">Verificar robots.txt</h3>
                <p class="text-sm text-gray-600 mt-1">Visualiza el archivo robots.txt actual.</p>
                <a href="{{ url('robots.txt') }}" target="_blank" class="mt-3 inline-block text-blue-600 hover:text-blue-800 text-sm">
                    Ver robots.txt &rarr;
                </a>
            </div>

            <div class="border rounded-lg p-4">
                <h3 class="font-medium text-gray-800">Verificar sitemap.xml</h3>
                <p class="text-sm text-gray-600 mt-1">Visualiza el sitemap principal actual.</p>
                <a href="{{ url('sitemap.xml') }}" target="_blank" class="mt-3 inline-block text-blue-600 hover:text-blue-800 text-sm">
                    Ver sitemap.xml &rarr;
                </a>
            </div>

            <div class="border rounded-lg p-4">
                <h3 class="font-medium text-gray-800">Limpiar caché</h3>
                <p class="text-sm text-gray-600 mt-1">Limpia la caché de metadatos y redirecciones.</p>
                <form action="{{ route('admin.cache.clear', ['type' => 'seo']) }}" method="POST" class="mt-3">
                    @csrf
                    <button type="submit" class="text-blue-600 hover:text-blue-800 text-sm bg-transparent border-0 p-0">
                        Limpiar caché &rarr;
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Enlaces útiles -->
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Enlaces útiles</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <a href="https://developers.google.com/search/docs" target="_blank" class="block p-4 border rounded-lg hover:bg-gray-50 transition-colors">
                <h3 class="font-medium text-gray-800">Documentación de Google Search</h3>
                <p class="text-sm text-gray-600 mt-1">Guías oficiales de Google para optimización de motores de búsqueda.</p>
            </a>

            <a href="https://search.google.com/search-console" target="_blank" class="block p-4 border rounded-lg hover:bg-gray-50 transition-colors">
                <h3 class="font-medium text-gray-800">Google Search Console</h3>
                <p class="text-sm text-gray-600 mt-1">Supervisa la presencia de tu sitio en los resultados de búsqueda de Google.</p>
            </a>

            <a href="https://schema.org/" target="_blank" class="block p-4 border rounded-lg hover:bg-gray-50 transition-colors">
                <h3 class="font-medium text-gray-800">Schema.org</h3>
                <p class="text-sm text-gray-600 mt-1">Referencia para datos estructurados y Rich Snippets.</p>
            </a>

            <a href="https://www.json-ld.org/" target="_blank" class="block p-4 border rounded-lg hover:bg-gray-50 transition-colors">
                <h3 class="font-medium text-gray-800">JSON-LD</h3>
                <p class="text-sm text-gray-600 mt-1">Información sobre el formato JSON-LD para datos estructurados.</p>
            </a>
        </div>
    </div>
</div>
@endsection