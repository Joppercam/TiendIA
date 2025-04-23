<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>
            
            @if(auth()->check() && auth()->user()->hasRole(['admin', 'super-admin']))
                <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Panel de Administración</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <a href="{{ route('admin.products.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-center">
                                Productos
                            </a>
                            <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-center">
                                Categorías
                            </a>
                            <a href="{{ route('admin.brands.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-center">
                                Marcas
                            </a>
                            <a href="{{ route('admin.inventory.dashboard') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-center">
                                Inventario
                            </a>
                            <a href="{{ route('admin.inventory.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-center">
                                Gestión de Inventario
                            </a>
                            <a href="{{ route('admin.suppliers.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-center">
                                Proveedores
                            </a>
                            <a href="{{ route('admin.inventory-movements.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-center">
                                Movimientos
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>