@extends('admin.layouts.app')

@section('title', 'Editar Redirección')

@section('content')
<div class="container py-6">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Editar Redirección</h1>
                <p class="mt-1 text-gray-600">Modifica una redirección existente.</p>
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
            <form action="{{ route('admin.seo.redirects.update', $redirect) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="source_url" class="block text-sm font-medium text-gray-700 mb-1">
                            URL de Origen
                            <span class="text-xs text-gray-500 font-normal ml-1">(Ruta relativa desde la raíz, ej: /productos-antiguos)</span>
                        </label>
                        <input type="text" name="source_url" id="source_url" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            value="{{ old('source_url', $redirect->source_url) }}"
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
                            value="{{ old('target_url', $redirect->target_url) }}"
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
                            <option value="301" {{ old('status_code', $redirect->status_code) == '301' ? 'selected' : '' }}>Permanente (301)</option>
                            <option value="302" {{ old('status_code', $redirect->status_code) == '302' ? 'selected' : '' }}>Temporal (302)</option>
                        </select>
                        @error('status_code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="is_active" id="is_active"
                                class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
                                {{ old('is_active', $redirect->is_active) ? 'checked' : '' }}>
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="is_active" class="font-medium text-gray-700">Activa</label>
                            <p class="text-gray-500">Indica si la redirección está activa.</p>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-md">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Estadísticas</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-500">Número de accesos:</p>
                                <p class="text-sm font-medium">{{ $redirect->hits }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Último acceso:</p>
                                <p class="text-sm font-medium">
                                    {{ $redirect->last_accessed_at ? \Carbon\Carbon::parse($redirect->last_accessed_at)->format('d/m/Y H:i') : 'Nunca' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Actualizar Redirección
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection