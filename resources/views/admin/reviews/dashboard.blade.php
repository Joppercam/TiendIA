@extends('admin.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-8">Dashboard de Valoraciones y Reseñas</h1>

    <!-- Tarjetas de estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-600">Total de Reseñas</h3>
            <p class="text-3xl font-bold mt-2">{{ $stats['total_reviews'] }}</p>
            <div class="mt-2 text-sm">
                <span class="text-green-600">{{ $stats['approved_reviews'] }}</span> aprobadas / 
                <span class="text-orange-500">{{ $stats['pending_reviews'] }}</span> pendientes
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-600">Valoración Media</h3>
            <div class="flex items-center mt-2">
                <span class="text-3xl font-bold mr-2">{{ number_format($stats['average_rating'], 1) }}</span>
                <div class="flex text-yellow-400">
                    @for ($i = 1; $i <= 5; $i++)
                        @if ($i <= round($stats['average_rating']))
                            <i class="fas fa-star"></i>
                        @else
                            <i class="far fa-star"></i>
                        @endif
                    @endfor
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-600">Total de Preguntas</h3>
            <p class="text-3xl font-bold mt-2">{{ $stats['total_questions'] }}</p>
            <div class="mt-2 text-sm">
                <span class="text-orange-500">{{ $stats['pending_questions'] }}</span> pendientes de aprobación
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-600">Total de Respuestas</h3>
            <p class="text-3xl font-bold mt-2">{{ $stats['total_answers'] }}</p>
            <div class="mt-2 text-sm">
                <span class="text-orange-500">{{ $stats['pending_answers'] }}</span> pendientes de aprobación
            </div>
        </div>
    </div>

    <!-- Acciones rápidas -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-xl font-bold mb-4">Acciones Rápidas</h2>
        <div class="flex flex-wrap gap-4">
            <a href="{{ route('admin.reviews.pending') }}" class="bg-blue-100 text-blue-700 px-4 py-2 rounded hover:bg-blue-200">
                Ver reseñas pendientes ({{ $stats['pending_reviews'] }})
            </a>
            <a href="{{ route('admin.questions.pending') }}" class="bg-blue-100 text-blue-700 px-4 py-2 rounded hover:bg-blue-200">
                Ver preguntas pendientes ({{ $stats['pending_questions'] }})
            </a>
            <a href="{{ route('admin.answers.pending') }}" class="bg-blue-100 text-blue-700 px-4 py-2 rounded hover:bg-blue-200">
                Ver respuestas pendientes ({{ $stats['pending_answers'] }})
            </a>
        </div>
    </div>

    <!-- Productos mejor valorados -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-xl font-bold mb-4">Productos Mejor Valorados</h2>
        
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b border-gray-200 text-left">Producto</th>
                        <th class="py-2 px-4 border-b border-gray-200 text-center">Valoración</th>
                        <th class="py-2 px-4 border-b border-gray-200 text-center">Total Valoraciones</th>
                        <th class="py-2 px-4 border-b border-gray-200 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($topRatedProducts as $product)
                        <tr>
                            <td class="py-2 px-4 border-b border-gray-200">
                                <a href="{{ route('products.show', $product) }}" class="text-blue-600 hover:text-blue-800">
                                    {{ $product->name }}
                                </a>
                            </td>
                            <td class="py-2 px-4 border-b border-gray-200 text-center">
                                <div class="flex items-center justify-center">
                                    <span class="font-bold mr-2">{{ number_format($product->average_rating, 1) }}</span>
                                    <div class="flex text-yellow-400">
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= round($product->average_rating))
                                                <i class="fas fa-star"></i>
                                            @else
                                                <i class="far fa-star"></i>
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                            </td>
                            <td class="py-2 px-4 border-b border-gray-200 text-center">
                                {{ $product->ratings_count }}
                            </td>
                            <td class="py-2 px-4 border-b border-gray-200 text-center">
                                <a href="{{ route('products.reviews.index', $product) }}" class="text-blue-600 hover:text-blue-800">
                                    Ver reseñas
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Productos peor valorados -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4">Productos Peor Valorados</h2>
        
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b border-gray-200 text-left">Producto</th>
                        <th class="py-2 px-4 border-b border-gray-200 text-center">Valoración</th>
                        <th class="py-2 px-4 border-b border-gray-200 text-center">Total Valoraciones</th>
                        <th class="py-2 px-4 border-b border-gray-200 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lowRatedProducts as $product)
                        <tr>
                            <td class="py-2 px-4 border-b border-gray-200">
                                <a href="{{ route('products.show', $product) }}" class="text-blue-600 hover:text-blue-800">
                                    {{ $product->name }}
                                </a>
                            </td>
                            <td class="py-2 px-4 border-b border-gray-200 text-center">
                                <div class="flex items-center justify-center">
                                    <span class="font-bold mr-2">{{ number_format($product->average_rating, 1) }}</span>
                                    <div class="flex text-yellow-400">
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= round($product->average_rating))
                                                <i class="fas fa-star"></i>
                                            @else
                                                <i class="far fa-star"></i>
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                            </td>
                            <td class="py-2 px-4 border-b border-gray-200 text-center">
                                {{ $product->ratings_count }}
                            </td>
                            <td class="py-2 px-4 border-b border-gray-200 text-center">
                                <a href="{{ route('products.reviews.index', $product) }}" class="text-blue-600 hover:text-blue-800">
                                    Ver reseñas
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection