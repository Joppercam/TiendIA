@extends('admin.layouts.app')

@section('title', 'Gestión de Pagos')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Gestión de Pagos</h1>
    </div>
    
    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form action="{{ route('admin.payments.index') }}" method="GET" class="flex flex-wrap gap-4">
            <div class="w-full md:w-auto">
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select name="status" class="form-select rounded-md border-gray-300 w-full">
                    <option value="">Todos los estados</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendiente</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completado</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Fallido</option>
                    <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Reembolsado</option>
                </select>
            </div>
            
            <div class="w-full md:w-auto">
                <label class="block text-sm font-medium text-gray-700 mb-1">Pasarela</label>
                <select name="gateway" class="form-select rounded-md border-gray-300 w-full">
                    <option value="">Todas las pasarelas</option>
                    @foreach($gateways as $gateway)
                    <option value="{{ $gateway->code }}" {{ request('gateway') == $gateway->code ? 'selected' : '' }}>
                        {{ $gateway->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            
            <div class="w-full md:w-auto">
                <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nº Orden o Referencia" class="form-input rounded-md border-gray-300 w-full">
            </div>
            
            <div class="w-full md:w-auto flex items-end">
                <button type="submit" class="bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700">
                    Filtrar
                </button>
                
                <!-- resources/views/admin/payments/index.blade.php (continuación) -->
                <a href="{{ route('admin.payments.index') }}" class="ml-2 bg-gray-200 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-300">
                    Limpiar
                </a>
            </div>
        </form>
    </div>
    
    <!-- Tabla de Pagos -->
    <div class="bg-white rounded-lg shadow-md overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        ID
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Orden
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Pasarela
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Monto
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Estado
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Fecha
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Acciones
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($payments as $payment)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $payment->id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <a href="{{ route('admin.orders.show', $payment->order_id) }}" class="text-indigo-600 hover:text-indigo-900">
                                #{{ $payment->order->order_number }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $payment->paymentGateway->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            ${{ number_format($payment->amount, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($payment->status == 'completed') bg-green-100 text-green-800
                                @elseif($payment->status == 'pending') bg-yellow-100 text-yellow-800
                                @elseif($payment->status == 'failed') bg-red-100 text-red-800
                                @elseif($payment->status == 'refunded') bg-purple-100 text-purple-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $payment->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.payments.show', $payment->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                Ver
                            </a>
                            
                            @if($payment->status == 'pending')
                                <form action="{{ route('admin.payments.verify', $payment->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-yellow-600 hover:text-yellow-900 mr-3">
                                        Verificar
                                    </button>
                                </form>
                                
                                <form action="{{ route('admin.payments.mark-as-paid', $payment->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-900" onclick="return confirm('¿Confirmar este pago manualmente?')">
                                        Confirmar
                                    </button>
                                </form>
                            @endif
                            
                            @if($payment->status == 'completed' && $payment->invoice)
                                <a href="{{ route('admin.payments.invoice.download', $payment->id) }}" class="text-gray-600 hover:text-gray-900">
                                    Factura
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            No se encontraron pagos
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Paginación -->
    <div class="mt-4">
        {{ $payments->appends(request()->query())->links() }}
    </div>
</div>
@endsection