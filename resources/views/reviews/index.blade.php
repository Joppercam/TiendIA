@extends('layouts.app')

@section('title', 'Reseñas de ' . $product->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('products.show', $product) }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-2"></i> Volver al producto
        </a>
    </div>

    <h1 class="text-2xl font-bold mb-6">Reseñas de {{ $product->name }}</h1>

    <!-- Resumen de valoraciones -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <div class="flex items-center mb-4">
            <div class="mr-4">
                <span class="text-5xl font-bold">{{ number_format($ratingSummary['average'], 1) }}</span>
                <div class="text-sm text-gray-500">de 5 estrellas</div>
            </div>
            <div class="flex-1">
                @for ($i = 5; $i >= 1; $i--)
                    <div class="flex items-center mb-1">
                        <div class="w-16 text-sm">{{ $i }} estrellas</div>
                        <div class="flex-1 ml-4">
                            <div class="bg-gray-200 h-4 rounded-full">
                                <div class="bg-yellow-400 h-4 rounded-full" style="width: {{ $ratingSummary['distribution'][$i]['percentage'] }}%"></div>
                            </div>
                        </div>
                        <div class="ml-4 text-sm text-gray-600 w-16">{{ $ratingSummary['distribution'][$i]['percentage'] }}%</div>
                    </div>
                @endfor
            </div>
        </div>
        <div class="text-sm text-gray-600">
            Basado en {{ $ratingSummary['count'] }} valoraciones
        </div>
        
        @auth
            <div class="mt-4">
                <a href="{{ route('products.reviews.create', $product) }}" 
                   class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Escribir una reseña
                </a>
            </div>
        @else
            <div class="mt-4">
                <a href="{{ route('login') }}" 
                   class="text-blue-600 hover:text-blue-800">
                    Inicia sesión para escribir una reseña
                </a>
            </div>
        @endauth
    </div>

    <!-- Listado de reseñas -->
    <div class="space-y-6">
        @forelse ($reviews as $review)
            <div class="bg-white rounded-lg shadow p-6 {{ $review->is_featured ? 'border-2 border-yellow-400' : '' }}">
                @if ($review->is_featured)
                    <div class="bg-yellow-400 text-yellow-800 px-2 py-1 rounded text-xs font-bold inline-block mb-2">
                        Reseña destacada
                    </div>
                @endif
                
                <div class="flex items-start">
                    <div class="mr-4">
                        <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center">
                            <span class="text-gray-600 font-bold">{{ substr($review->user->name, 0, 1) }}</span>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-lg">{{ $review->title }}</h3>
                        <div class="flex items-center mb-2">
                            <!-- Estrellas -->
                            <div class="flex text-yellow-400 mr-2">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= $review->rating->score)
                                        <i class="fas fa-star"></i>
                                    @else
                                        <i class="far fa-star"></i>
                                    @endif
                                @endfor
                            </div>
                            <span class="text-sm text-gray-500">
                                {{ $review->user->name }} - {{ $review->created_at->format('d/m/Y') }}
                            </span>
                            @if ($review->is_verified_purchase)
                                <span class="ml-2 bg-green-100 text-green-800 text-xs font-medium px-2 py-0.5 rounded">
                                    Compra verificada
                                </span>
                            @endif
                        </div>
                        <div class="text-gray-700 mb-2">
                            {{ $review->comment }}
                        </div>
                        
                        @auth
                            @if (auth()->id() === $review->user_id)
                                <div class="mt-2 flex space-x-2">
                                    <a href="{{ route('products.reviews.edit', [$product, $review]) }}" 
                                       class="text-blue-600 hover:text-blue-800 text-sm">
                                        Editar
                                    </a>
                                    <form action="{{ route('products.reviews.destroy', [$product, $review]) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm"
                                                onclick="return confirm('¿Estás seguro de que deseas eliminar esta reseña?')">
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600">
                    Aún no hay reseñas para este producto. ¡Sé el primero en compartir tu opinión!
                </p>
            </div>
        @endforelse
    </div>
    
    <!-- Paginación -->
    <div class="mt-6">
        {{ $reviews->links() }}
    </div>
</div>
@endsection