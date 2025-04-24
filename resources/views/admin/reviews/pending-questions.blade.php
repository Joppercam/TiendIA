@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Preguntas Pendientes de Aprobación</h1>
        <a href="{{ route('admin.reviews.dashboard') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300">
            Volver al Dashboard
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if ($questions->count() > 0)
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-3 px-4 border-b border-gray-200 text-left">Producto</th>
                        <th class="py-3 px-4 border-b border-gray-200 text-left">Pregunta</th>
                        <th class="py-3 px-4 border-b border-gray-200 text-center">Usuario</th>
                        <th class="py-3 px-4 border-b border-gray-200 text-center">Fecha</th>
                        <th class="py-3 px-4 border-b border-gray-200 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($questions as $question)
                        <tr>
                            <td class="py-3 px-4 border-b border-gray-200">
                                <a href="{{ route('products.show', $question->product) }}" class="text-blue-600 hover:text-blue-800">
                                    {{ $question->product->name }}
                                </a>
                            </td>
                            <td class="py-3 px-4 border-b border-gray-200">
                                {{ $question->question }}
                            </td>
                            <td class="py-3 px-4 border-b border-gray-200 text-center">
                                {{ $question->user->name }}
                            </td>
                            <td class="py-3 px-4 border-b border-gray-200 text-center">
                                {{ $question->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-3 px-4 border-b border-gray-200">
                                <div class="flex justify-center space-x-2">
                                    <form action="{{ route('admin.questions.approve', $question) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">
                                            Aprobar
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.questions.reject', $question) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas rechazar esta pregunta?')">
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
                <p class="text-gray-600">No hay preguntas pendientes de aprobación.</p>
            </div>
        @endif
    </div>
    
    <!-- Paginación -->
    <div class="mt-6">
        {{ $questions->links() }}
    </div>
</div>
@endsection