@extends('layouts.app')

@section('title', '¡Pago Exitoso!')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 mb-4">
            <svg class="h-10 w-10 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
        </div>
        <h1 class="text-3xl font-bold text-gray-900">¡Pago Exitoso!</h1>
        <p class="text-gray-600 mt-2">Tu pago ha sido procesado correctamente.</p>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Detalles de la Orden</h2>
        
        <div class="space-y-3 mb-6">
            <div class="flex">
                <span class="w-1/3 font-medium">Número de Orden:</span>
                <span>{{ $order->order_number }}</span>
            </div>
            <div class="flex">
                <span class="w-1/3 font-medium">Fecha:</span>
                <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="flex">
                <span class="w-1/3 font-medium">Total:</span>
                <span class="font-bold">${{ number_format($order->total, 0, ',', '.') }}</span>
            </div>
            <div class="flex">
                <span class="w-1/3 font-medium">Estado:</span>
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                    Pagado
                </span>
            </div>
        </div>
        
        <div class="border-t border-gray-200 pt-4">
            <p class="text-gray-700">Recibirás un correo electrónico con los detalles de tu compra. Tu pedido será procesado a la brevedad.</p>
        </div>
    </div>
    
    <div class="flex justify-between">
        <a href="{{ route('orders.show', $order->id) }}" class="bg-indigo-600 text-white py-2 px-6 rounded hover:bg-indigo-700 transition-colors">
            Ver Detalle de la Orden
        </a>
        
        @if($order->invoice)
        <a href="{{ route('orders.invoice.download', $order->id) }}" class="text-indigo-600 py-2 px-6 border border-indigo-600 rounded hover:bg-indigo-50 transition-colors">
            Descargar Factura
        </a>
        @endif
        
        <a href="{{ route('home') }}" class="text-gray-600 py-2 px-6 border border-gray-300 rounded hover:bg-gray-50 transition-colors">
            Volver a la Tienda
        </a>
    </div>
</div>
@endsection