@extends('layouts.app')

@section('title', 'Preguntas sobre ' . $product->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('products.show', $product) }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-2"></i> Volver al producto
        </a>
    </div>

    <h1 class="text-2xl font-bold mb-6">Preguntas sobre {{ $product->name }}</h1>

    <!-- Formulario para hacer una pregunta -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-xl font-bold mb-4">¿Tienes alguna duda sobre este producto?</h2>
        
        @auth
            <form action="{{ route('products.questions.store', $product) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <textarea name="question" rows="3" 
                              class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-blue-500"
                              placeholder="Escribe tu pregunta aquí..." required>{{ old('question') }}</textarea>
                    @error('question')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Enviar pregunta
                    </button>
                </div>
            </form>
        @else
            <p class="text-gray-600">
                <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800">Inicia sesión</a> para hacer una pregunta.
            </p>
        @endauth
    </div>

    <!-- Listado de preguntas y respuestas -->
    <div class="space-y-6">
        @forelse ($questions as $question)
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-start mb-4">
                    <div class="mr-4">
                        <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center">
                            <span class="text-gray-600 font-bold">{{ substr($question->user->name, 0, 1) }}</span>
                        </div>
                    </div>
                    <div class="flex-1">
                        <p class="font-bold mb-1">Pregunta:</p>
                        <p class="text-gray-700 mb-1">{{ $question->question }}</p>
                        <div class="text-sm text-gray-500">
                            {{ $question->user->name }} - {{ $question->created_at->format('d/m/Y') }}
                        </div>
                        
                        @auth
                            @if (auth()->id() === $question->user_id)
                                <div class="mt-2 flex space-x-2">
                                    <a href="{{ route('products.questions.edit', [$product, $question]) }}" 
                                       class="text-blue-600 hover:text-blue-800 text-sm">
                                        Editar
                                    </a>
                                    <form action="{{ route('products.questions.destroy', [$product, $question]) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm"
                                                onclick="return confirm('¿Estás seguro de que deseas eliminar esta pregunta?')">
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            @endif
                        @endauth
                    </div>
                </div>
                
                <!-- Respuestas -->
                @if ($question->answers->count() > 0)
                    <div class="pl-14 space-y-4">
                        @foreach ($question->answers as $answer)
                            <div class="border-l-2 border-gray-200 pl-4 {{ $answer->is_from_seller ? 'bg-blue-50 p-3 rounded' : '' }}">
                                <div class="flex items-start">
                                    <div class="mr-4">
                                        <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center">
                                            <span class="text-gray-600 font-bold text-sm">{{ substr($answer->user->name, 0, 1) }}</span>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-bold mb-1">
                                            Respuesta:
                                            @if ($answer->is_from_seller)
                                                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-0.5 rounded ml-2">
                                                    Vendedor
                                                </span>
                                            @endif
                                        </p>
                                        <p class="text-gray-700 mb-1">{{ $answer->answer }}</p>
                                        <div class="text-sm text-gray-500">
                                            {{ $answer->user->name }} - {{ $answer->created_at->format('d/m/Y') }}
                                        </div>
                                        
                                        @auth
                                            @if (auth()->id() === $answer->user_id)
                                                <div class="mt-2 flex space-x-2">
                                                    <a href="{{ route('products.questions.answers.edit', [$product, $question, $answer]) }}" 
                                                       class="text-blue-600 hover:text-blue-800 text-sm">
                                                        Editar
                                                    </a>
                                                    <form action="{{ route('products.questions.answers.destroy', [$product, $question, $answer]) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm"
                                                                onclick="return confirm('¿Estás seguro de que deseas eliminar esta respuesta?')">
                                                            Eliminar
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
                
                <!-- Formulario para responder -->
                @auth
                    <div class="mt-4 pl-14">
                    <button class="text-blue-600 hover:text-blue-800 text-sm toggle-answer-form">
                            Responder
                        </button>
                        
                        <div class="answer-form mt-2 hidden">
                            <form action="{{ route('products.questions.answers.store', [$product, $question]) }}" method="POST">
                                @csrf
                                <div class="mb-2">
                                    <textarea name="answer" rows="2" 
                                            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-blue-500"
                                            placeholder="Escribe tu respuesta aquí..." required></textarea>
                                </div>
                                <div class="flex justify-end">
                                    <button type="button" class="text-gray-600 hover:text-gray-800 px-3 py-1 mr-2 cancel-answer">
                                        Cancelar
                                    </button>
                                    <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                                        Enviar respuesta
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endauth
            </div>
        @empty
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600">
                    Aún no hay preguntas sobre este producto. ¡Sé el primero en preguntar!
                </p>
            </div>
        @endforelse
    </div>
    
    <!-- Paginación -->
    <div class="mt-6">
        {{ $questions->links() }}
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mostrar/ocultar formulario de respuesta
        const toggleButtons = document.querySelectorAll('.toggle-answer-form');
        const cancelButtons = document.querySelectorAll('.cancel-answer');
        
        toggleButtons.forEach(button => {
            button.addEventListener('click', function() {
                const form = this.nextElementSibling;
                form.classList.toggle('hidden');
                this.classList.add('hidden');
            });
        });
        
        cancelButtons.forEach(button => {
            button.addEventListener('click', function() {
                const form = this.closest('.answer-form');
                const toggleButton = form.previousElementSibling;
                form.classList.add('hidden');
                toggleButton.classList.remove('hidden');
            });
        });
    });
</script>
@endpush
@endsection