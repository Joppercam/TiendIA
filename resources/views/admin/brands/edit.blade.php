@extends('admin.layouts.app')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Editar Marca: {{ $brand->name }}</h2>
                <a href="{{ route('admin.brands.index') }}" class="bg-gray-500 text-white py-2 px-4 rounded hover:bg-gray-600">
                    Volver
                </a>
            </div>

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.brands.update', $brand) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Nombre:</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $brand->name) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Descripción:</label>
                    <textarea name="description" id="description" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('description', $brand->description) }}</textarea>
                </div>

                <div class="mb-4">
                    @if($brand->logo)
                        <div class="mb-2">
                            <img src="{{ asset('storage/brands/thumbnails/' . $brand->logo) }}" alt="{{ $brand->name }}" class="h-24 w-24 object-cover rounded">
                        </div>
                    @endif
                    <label for="logo" class="block text-gray-700 text-sm font-bold mb-2">Logo:</label>
                    <input type="file" name="logo" id="logo" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <p class="text-sm text-gray-600 mt-1">Deja en blanco para mantener el logo actual.</p>
                </div>

                <div class="mb-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $brand->is_active) ? 'checked' : '' }} class="form-checkbox h-5 w-5 text-indigo-600">
                        <span class="ml-2 text-gray-700">Activa</span>
                    </label>
                </div>

                <div class="flex items-center justify-end">
                    <button type="submit" class="bg-indigo-600 text-white py-2 px-4 rounded hover:bg-indigo-700 focus:outline-none focus:shadow-outline">
                        Actualizar Marca
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection