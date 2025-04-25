@extends('layouts.admin')

@section('title', 'Gestión de Pasarelas de Pago')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Gestión de Pasarelas de Pago</h1>
        <div>
            <a href="{{ route('admin.payment-gateways.create') }}" class="bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700">
                Nueva Pasarela
            </a>
        </div>
    </div>
    
    <!-- Tabla de Pasarelas -->
    <div class="bg-white rounded-lg shadow-md overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Posición
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Nombre
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Código
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Estado
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Predeterminada
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Acciones
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($gateways as $gateway)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <div class="flex items-center">
                                <form action="{{ route('admin.payment-gateways.position', $gateway->id) }}" method="POST" class="inline mr-1">
                                    @csrf
                                    <input type="hidden" name="direction" value="up">
                                    <button type="submit" class="text-gray-500 hover:text-gray-700" {{ $gateway->position == 0 ? 'disabled' : '' }}>
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                        </svg>
                                    </button>
                                </form>
                                
                                <form action="{{ route('admin.payment-gateways.position', $gateway->id) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="direction" value="down">
                                    <button type="submit" class="text-gray-500 hover:text-gray-700" {{ $loop->last ? 'disabled' : '' }}>
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                </form>
                                
                                <span class="ml-2">{{ $gateway->position }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div class="flex items-center">
                                @if($gateway->logo)
                                    <img src="{{ asset('storage/' . $gateway->logo) }}" alt="{{ $gateway->name }}" class="h-8 mr-2">
                                @endif
                                {{ $gateway->name }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $gateway->code }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <form action="{{ route('admin.payment-gateways.toggle-active', $gateway->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="flex items-center">
                                    <span class="mr-2 w-8 h-4 flex items-center {{ $gateway->is_active ? 'bg-green-400' : 'bg-gray-300' }} rounded-full transition-colors duration-200">
                                        <span class="w-3 h-3 bg-white rounded-full transform transition-transform duration-200 {{ $gateway->is_active ? 'translate-x-4' : 'translate-x-1' }}"></span>
                                    </span>
                                    <span class="text-sm {{ $gateway->is_active ? 'text-green-600' : 'text-gray-500' }}">
                                        {{ $gateway->is_active ? 'Activa' : 'Inactiva' }}
                                    </span>
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($gateway->is_default)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Predeterminada
                                </span>
                            @else
                                <span class="text-gray-500">No</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.payment-gateways.edit', $gateway->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                Editar
                            </a>
                            
                            <form action="{{ route('admin.payment-gateways.destroy', $gateway->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('¿Estás seguro de eliminar esta pasarela?')">
                                    Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            No hay pasarelas de pago configuradas
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection