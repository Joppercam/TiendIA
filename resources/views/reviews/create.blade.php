@extends('layouts.app')

@section('title', 'Escribir una reseña - ' . $product->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('products.show', $product) }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-2"></i> Volver al producto
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold mb-6">Escribir una reseña para {{ $product->name }}</h1>
        
        <div class="flex items-center mb-8">
            <div class="w-24">
                <img src="{{ $product->primary_image }}" alt="{{ $product->name }}" class="w-full rounded">
            </div>
            <div class="ml-4">
                <h2 class="text-lg font-bold">{{ $product->name }}</h2>
                <p class="text-gray-600">{{ $product->short_description }}</p>
            </div>
        </div>
        
        <form action="{{ route('products.reviews.store', $product) }}" method="POST">
            @csrf
            
            <div class="mb-6">
                <label class="block text-gray-700 font-bold mb-2">Tu valoración *</label>
                <div class="flex text-3xl text-gray-400 rating-stars">
                    @for ($i = 1; $i <= 5; $i++)
                        <i class="far fa-star cursor-pointer mx-1 star-rating" data-rating="{{ $i }}"></i>
                    @endfor
                </div>
                <input type="hidden" name="score" id="rating-input" value="0">
                @error('score')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-6">
                <label for="title" class="block text-gray-700 font-bold mb-2">Título de tu reseña *</label>
                <input type="text" name="title" id="title" 
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-blue-500"
                       value="{{ old('title') }}" required>
                @error('title')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-6">
                <label for="comment" class="block text-gray-700 font-bold mb-2">Tu reseña *</label>
                <textarea name="comment" id="comment" rows="6" 
                          class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-blue-500"
                          required>{{ old('comment') }}</textarea>
                @error('comment')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-sm text-gray-600 mt-1">
                    Mínimo 10 caracteres. Comparte tu experiencia con este producto.
                </p>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                    Enviar reseña
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const stars = document.querySelectorAll('.star-rating');
        const ratingInput = document.getElementById('rating-input');
        
        stars.forEach(star => {
            star.addEventListener('click', function() {
                const rating = this.dataset.rating;
                ratingInput.value = rating;
                
                // Reset all stars
                stars.forEach(s => {
                    s.className = 'far fa-star cursor-pointer mx-1 star-rating';
                });
                
                // Fill stars up to selected rating
                stars.forEach(s => {
                    if (s.dataset.rating <= rating) {
                        s.className = 'fas fa-star cursor-pointer mx-1 star-rating text-yellow-400';
                    }
                });
            });
            
            star.addEventListener('mouseover', function() {
                const rating = this.dataset.rating;
                
                // Reset all stars
                stars.forEach(s => {
                    if (s.dataset.rating <= ratingInput.value) {
                        s.className = 'fas fa-star cursor-pointer mx-1 star-rating text-yellow-400';
                    } else {
                        s.className = 'far fa-star cursor-pointer mx-1 star-rating';
                    }
                });
                
                // Fill stars up to hovered rating
                stars.forEach(s => {
                    if (s.dataset.rating <= rating) {
                        s.className = 'fas fa-star cursor-pointer mx-1 star-rating text-yellow-400';
                    }
                });
            });
            
            star.addEventListener('mouseout', function() {
                // Reset to selected rating
                stars.forEach(s => {
                    if (s.dataset.rating <= ratingInput.value) {
                        s.className = 'fas fa-star cursor-pointer mx-1 star-rating text-yellow-400';
                    } else {
                        s.className = 'far fa-star cursor-pointer mx-1 star-rating';
                    }
                });
            });
        });
    });
</script>
@endpush
@endsection