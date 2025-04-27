@extends('admin.layouts.app')

@section('title', 'Gestión de Sitemaps')

@section('content')
<div class="container py-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800">Gestión de Sitemaps</h1>
            <p class="mt-1 text-gray-600">Administra la configuración y generación de sitemaps XML.</p>
        </div>
        <div class="flex items-center space-x-4">
            <form action="{{ route('admin.seo.sitemaps.generate') }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    Generar Sitemaps
                </button>
            </form>
            <a href="{{ route('admin.seo.sitemaps.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                Nueva Configuración
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nombre
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tipo
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Frecuencia
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Prioridad
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Estado
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Última Generación
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($sitemaps as $sitemap)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $sitemap->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @php
                                    $typeLabels = [
                                        'products' => 'Productos',
                                        'categories' => 'Categorías',
                                        'brands' => 'Marcas',
                                        'pages' => 'Páginas',
                                    ];
                                    $typeLabel = $typeLabels[$sitemap->type] ?? $sitemap->type;
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $sitemap->type == 'products' ? 'bg-blue-100 text-blue-800' : 
                                       ($sitemap->type == 'categories' ? 'bg-green-100 text-green-800' : 
                                       ($sitemap->type == 'brands' ? 'bg-purple-100 text-purple-800' : 
                                                                  'bg-gray-100 text-gray-800')) }}">
                                    {{ $typeLabel }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @php
                                    $frequencyLabels = [
                                        'always' => 'Siempre',
                                        'hourly' => 'Cada hora',
                                        'daily' => 'Diario',
                                        'weekly' => 'Semanal',
                                        'monthly' => 'Mensual',
                                        'yearly' => 'Anual',
                                        'never' => 'Nunca',
                                    ];
                                    $frequencyLabel = $frequencyLabels[$sitemap->frequency] ?? $sitemap->frequency;
                                @endphp
                                {{ $frequencyLabel }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $sitemap->priority }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $sitemap->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $sitemap->is_active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $sitemap->last_generated_at ? $sitemap->last_generated_at->format('d/m/Y H:i') : 'Nunca' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-3">
                                    @if($sitemap->filepath)
                                        <a href="{{ Storage::disk('public')->url($sitemap->filepath) }}" target="_blank" class="text-green-600 hover:text-green-900">
                                            Ver
                                        </a>
                                    @endif
                                    <a href="{{ route('admin.seo.sitemaps.edit', $sitemap) }}" class="text-indigo-600 hover:text-indigo-900">
                                        Editar
                                    </a>
                                    <form action="{{ route('admin.seo.sitemaps.destroy', $sitemap) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta configuración?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                No hay configuraciones de sitemap definidas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6 bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Enlaces de Sitemaps</h2>
        <div class="space-y-3">
            <div class="flex items-center space-x-4">
                <span class="text-sm font-medium text-gray-700">Sitemap Principal:</span>
                <a href="{{ url('sitemap.xml') }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                    {{ url('sitemap.xml') }}
                </a>
            </div>
            @foreach($sitemaps as $sitemap)
                @if($sitemap->filepath)
                <div class="flex items-center space-x-4">
                    <span class="text-sm font-medium text-gray-700">Sitemap de {{ $typeLabels[$sitemap->type] ?? $sitemap->type }}:</span>
                    <a href="{{ Storage::disk('public')->url($sitemap->filepath) }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                        {{ url(Storage::disk('public')->url($sitemap->filepath)) }}
                    </a>
                </div>
                @endif
            @endforeach
        </div>
    </div>
</div>
@endsection