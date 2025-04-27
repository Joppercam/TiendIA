@extends('admin.layouts.app')

@section('title', 'Crear Redirección')

@section('content')
<div class="container py-6">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Crear Redirección</h1>
                <p class="mt-1 text-gray-600">Configura una nueva redirección.</p>
            </div>
            <div>
                <a href="{{ route('admin.seo.redirects.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                    Volver
                </a>
            </div>
        </div>
    </div>

    @if(session('error'))
        <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <form action="{{ route('admin.seo.redirects.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="source_url" class="block text-sm font-medium text-gray-700 mb-1">
                            URL de Origen
                            <span class="text-xs text-gray-500 font-normal ml-1">(Ruta relativa desde la raíz, ej: /productos-antiguos)</span>
                        </label>
                        <input type="text" name="source_url" id="source_url" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            value="{{ old('source_url') }}"
                            placeholder="/ruta-antigua"
                            required>
                        @error('source_url')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="target_url" class="block text-sm font-medium text-gray-700 mb-1">
                            URL de Destino
                            <span class="text-xs text-gray-500 font-normal ml-1">(Ruta relativa o URL completa)</span>
                        </label>
                        <input type="text" name="target_url" id="target_url"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            value="{{ old('target_url') }}"
                            placeholder="/nueva-ruta o https://ejemplo.com/pagina"
                            required>
                        @error('target_url')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status_code" class="block text-sm font-medium text-gray-700 mb-1">
                            Tipo de Redirección
                        </label>
                        <select name="status_code" id="status_code"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="301" {{ old('status_code') == '301' ? 'selected' : '' }}>Permanente (301)</option>
                            <option value="302" {{ old('status_code') == '302' ? 'selected' : '' }}>Temporal (302)</option>
                        </select>
                        @error('status_code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">
                            <strong>Permanente (301):</strong> Indica que la página se ha movido permanentemente.<br>
                            <strong>Temporal (302):</strong> Indica que la página se ha movido temporalmente.
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Crear Redirección
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection