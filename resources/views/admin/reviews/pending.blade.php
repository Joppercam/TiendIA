@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Reseñas Pendientes de Aprobación</h1>
        <a href="{{ route('admin.reviews.dashboard') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300">
            Volver al Dashboard
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if ($reviews->count() > 0)
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-3 px-4 border-b border-gray-200 text-left">Producto</th>
                        <th class="py-3 px-4 border-b border-gray-200 text-left">Reseña</th>
                        <th class="py-3 px-4 border-b border-gray-200 text-center">Valoración</th>
                        <th class="py-3 px-4 border-b border-gray-200 text-center">Usuario</th>
                        <th class="py-3 px-4 border-b border-gray-200 text-center">Fecha</th>
                        <th class="py-3 px-4 border-b border-gray-200 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reviews as $review)
                        <tr>
                            <td class="py-3 px-4 border-b border-gray-200">
                                <a href="{{ route('products.show', $review->product) }}" class="text-blue-600 hover:text-blue-800">
                                    {{ $review->product->name }}
                                </a>
                            </td>
                            <td class="py-3 px-4 border-b border-gray-200">
                                <strong>{{ $review->title }}</strong>
                                <p class="text-gray-600 text-sm">{{ Str::limit($review->comment, 100) }}</p>
                            </td>
                            <td class="py-3 px-4 border-b border-gray-200 text-center">
                                <div class="flex items-center justify-center">
                                    <span class="mr-1 font-bold">{{ $review->rating->score }}</span>
                                    <div class="flex text-yellow-400">
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= $review->rating->score)
                                                <i class="fas fa-star"></i>
                                            @else
                                                <i class="far fa-star"></i>
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4 border-b border-gray-200 text-center">
                                {{ $review->user->name }}
                                @if ($review->is_verified_purchase)
                                    <span class="ml-1 bg-green-100 text-green-800 text-xs px-2 py-0.5 rounded">
                                        Compra verificada
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 border-b border-gray-200 text-center">
                                {{ $review->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-3 px-4 border-b border-gray-200">
                                <div class="flex justify-center space-x-2">
                                    <form action="{{ route('admin.reviews.approve', $review) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">
                                            Aprobar
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.reviews.reject', $review) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas rechazar esta reseña?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">
                                            Rechazar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="p-6 text-center">
                <p class="text-gray-600">No hay reseñas pendientes de aprobación.</p>
            </div>
        @endif
    </div>
    
    <!-- Paginación -->
    <div class="mt-6">
        {{ $reviews->links() }}
    </div>
</div>
@endsection