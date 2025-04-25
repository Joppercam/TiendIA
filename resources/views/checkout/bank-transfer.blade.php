@extends('layouts.app')

@section('title', 'Pago por Transferencia Bancaria')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Pago por Transferencia Bancaria</h1>
        <p class="text-gray-600">Orden #{{ $order->order_number }}</p>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex items-center mb-4 bg-blue-50 p-4 rounded-md">
            <svg class="h-8 w-8 text-blue-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <h3 class="font-semibold text-blue-700">Instrucciones de Pago</h3>
                <p class="text-blue-600">{{ $instructions }}</p>
            </div>
        </div>
        
        <h2 class="text-xl font-semibold mb-4">Datos Bancarios</h2>
        
        <div class="space-y-3 mb-6">
            <div class="flex">
                <span class="w-1/3 font-medium">Banco:</span>
                <span>{{ $bankInfo['bank_name'] }}</span>
            </div>
            <div class="flex">
                <span class="w-1/3 font-medium">Tipo de Cuenta:</span>
                <span>{{ $bankInfo['account_type'] }}</span>
            </div>
            <div class="flex">
                <span class="w-1/3 font-medium">Número de Cuenta:</span>
                <span>{{ $bankInfo['account_number'] }}</span>
            </div>
            <div class="flex">
                <span class="w-1/3 font-medium">RUT:</span>
                <span>{{ $bankInfo['rut'] }}</span>
            </div>
            <div class="flex">
                <span class="w-1/3 font-medium">Nombre:</span>
                <span>{{ $bankInfo['account_name'] }}</span>
            </div>
            <div class="flex">
                <span class="w-1/3 font-medium">Email para comprobante:</span>
                <span>{{ $bankInfo['email'] }}</span>
            </div>
            <div class="flex">
                <span class="w-1/3 font-medium">Monto a transferir:</span>
                <span class="font-bold">${{ number_format($order->total, 0, ',', '.') }}</span>
            </div>
        </div>
        
        <div class="bg-yellow-50 p-4 rounded-md mb-6">
            <p class="text-yellow-700">Importante: Al enviar el comprobante, incluye el número de orden <strong>{{ $order->order_number }}</strong> en el asunto del correo.</p>
        </div>
        
        <div class="flex justify-between">
            <a href="{{ route('checkout.payment.status', $order->id) }}" class="bg-indigo-600 text-white py-2 px-6 rounded hover:bg-indigo-700 transition-colors">
                Verificar Estado del Pago
            </a>
            
            <a href="{{ route('orders.show', $order->id) }}" class="text-indigo-600 py-2 px-6 border border-indigo-600 rounded hover:bg-indigo-50 transition-colors">
                Ver Detalle de la Orden
            </a>
        </div>
    </div>
</div>
@endsection