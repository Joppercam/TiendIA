@extends('admin.layouts.app')

@section('title', 'Detalle de Pago')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Detalle de Pago #{{ $payment->id }}</h1>
        <div>
            <a href="{{ route('admin.payments.index') }}" class="bg-gray-200 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-300">
                Volver
            </a>
        </div>
    </div>
    
    <!-- Información del Pago -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h2 class="text-xl font-semibold mb-4">Información del Pago</h2>
                <div class="space-y-3">
                    <div class="flex">
                        <span class="w-1/3 font-medium">ID:</span>
                        <span>{{ $payment->id }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-1/3 font-medium">Pasarela:</span>
                        <span>{{ $payment->paymentGateway->name }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-1/3 font-medium">Referencia:</span>
                        <span>{{ $payment->gateway_reference ?: 'N/A' }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-1/3 font-medium">Monto:</span>
                        <span>${{ number_format($payment->amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-1/3 font-medium">Estado:</span>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            @if($payment->status == 'completed') bg-green-100 text-green-800
                            @elseif($payment->status == 'pending') bg-yellow-100 text-yellow-800
                            @elseif($payment->status == 'failed') bg-red-100 text-red-800
                            @elseif($payment->status == 'refunded') bg-purple-100 text-purple-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </div>
                    <div class="flex">
                        <span class="w-1/3 font-medium">Fecha de Creación:</span>
                        <span>{{ $payment->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @if($payment->paid_at)
                    <div class="flex">
                        <span class="w-1/3 font-medium">Fecha de Pago:</span>
                        <span>{{ $payment->paid_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @endif
                </div>
            </div>
            
            <div>
                <h2 class="text-xl font-semibold mb-4">Información de la Orden</h2>
                <div class="space-y-3">
                    <div class="flex">
                        <span class="w-1/3 font-medium">Número de Orden:</span>
                        <span>
                            <a href="{{ route('admin.orders.show', $payment->order_id) }}" class="text-indigo-600 hover:text-indigo-900">
                                #{{ $payment->order->order_number }}
                            </a>
                        </span>
                    </div>
                    <div class="flex">
                        <span class="w-1/3 font-medium">Cliente:</span>
                        <span>{{ $payment->order->user->name }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-1/3 font-medium">Email:</span>
                        <span>{{ $payment->order->user->email }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-1/3 font-medium">Total:</span>
                        <span>${{ number_format($payment->order->total, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-1/3 font-medium">Estado de la Orden:</span>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            @if(in_array($payment->order->status, ['completed', 'delivered'])) bg-green-100 text-green-800
                            @elseif(in_array($payment->order->status, ['pending', 'processing'])) bg-yellow-100 text-yellow-800
                            @elseif(in_array($payment->order->status, ['cancelled', 'failed'])) bg-red-100 text-red-800
                            @elseif($payment->order->status == 'refunded') bg-purple-100 text-purple-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($payment->order->status) }}
                        </span>
                    </div>
                    <div class="flex">
                        <span class="w-1/3 font-medium">Fecha de la Orden:</span>
                        <span>{{ $payment->order->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Acciones -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Acciones</h2>
        
        <div class="flex flex-wrap gap-3">
            @if($payment->status == 'pending')
                <form action="{{ route('admin.payments.verify', $payment->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-yellow-500 text-white py-2 px-4 rounded-md hover:bg-yellow-600">
                        Verificar Estado
                    </button>
                </form>
                
                <form action="{{ route('admin.payments.mark-as-paid', $payment->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-green-500 text-white py-2 px-4 rounded-md hover:bg-green-600" onclick="return confirm('¿Estás seguro de marcar este pago como completado manualmente?')">
                        Marcar como Pagado
                    </button>
                </form>
            @endif
            
            @if($payment->status == 'completed')
                <button type="button" class="bg-red-500 text-white py-2 px-4 rounded-md hover:bg-red-600" onclick="toggleRefundForm()">
                    Procesar Reembolso
                </button>
                
                @if($payment->invoice)
                    <a href="{{ route('admin.payments.invoice.download', $payment->id) }}" class="bg-gray-500 text-white py-2 px-4 rounded-md hover:bg-gray-600">
                        Descargar Factura
                    </a>
                @endif
            @endif
        </div>
        
        <!-- Formulario de Reembolso (oculto por defecto) -->
        <div id="refundForm" class="hidden mt-6 p-4 border border-gray-200 rounded-md">
            <h3 class="font-medium mb-3">Procesar Reembolso</h3>
            
            <form action="{{ route('admin.payments.refund', $payment->id) }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Monto a Reembolsar</label>
                    <input type="number" name="amount" value="{{ $payment->amount }}" min="1" max="{{ $payment->amount }}" step="1" class="form-input rounded-md border-gray-300 w-full">
                    <p class="text-xs text-gray-500 mt-1">Deja en blanco para reembolsar el monto total.</p>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Motivo del Reembolso</label>
                    <input type="text" name="reason" class="form-input rounded-md border-gray-300 w-full" placeholder="Razón del reembolso">
                </div>
                
                <div class="flex justify-end">
                    <button type="button" class="bg-gray-200 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-300 mr-2" onclick="toggleRefundForm()">
                        Cancelar
                    </button>
                    <button type="submit" class="bg-red-500 text-white py-2 px-4 rounded-md hover:bg-red-600" onclick="return confirm('¿Estás seguro de procesar este reembolso?')">
                        Confirmar Reembolso
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Historial de Transacciones -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Historial de Transacciones</h2>
        
        @if($payment->transactions->isEmpty())
            <p class="text-gray-500">No hay transacciones registradas para este pago.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ID
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tipo
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Monto
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estado
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Referencia
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fecha
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($payment->transactions as $transaction)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $transaction->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ ucfirst($transaction->type) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    ${{ number_format($transaction->amount, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($transaction->status == 'completed') bg-green-100 text-green-800
                                        @elseif($transaction->status == 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($transaction->status == 'failed') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $transaction->reference ?: 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $transaction->created_at->format('d/m/Y H:i') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    
    <!-- Respuesta de la Pasarela -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Respuesta de la Pasarela</h2>
        
        <div class="bg-gray-50 p-4 rounded-md overflow-x-auto">
            <pre class="text-sm text-gray-700">{{ json_encode($payment->gateway_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
    </div>
</div>

<script>
    function toggleRefundForm() {
        const form = document.getElementById('refundForm');
        form.classList.toggle('hidden');
    }
</script>
@endsection