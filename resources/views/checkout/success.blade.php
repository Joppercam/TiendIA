<!-- resources/views/checkout/success.blade.php -->
@extends('layouts.app')

@section('title', 'Pedido Completado')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-md p-8">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            
            <h1 class="text-3xl font-bold text-gray-800 mb-2">¡Gracias por tu pedido!</h1>
            <p class="text-xl text-gray-600">Tu pedido ha sido completado con éxito.</p>
        </div>
        
        <div class="mb-8">
            <div class="bg-gray-50 rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">Detalles del pedido</h2>
                    <span class="text-sm text-gray-600">Fecha: {{ $order->created_at->format('d/m/Y H:i') }}</span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <p class="font-medium text-gray-700 mb-1">Número de pedido</p>
                        <p class="text-gray-600">{{ $order->order_number }}</p>
                    </div>
                    
                    <div>
                        <p class="font-medium text-gray-700 mb-1">Estado</p>
                        <p>
                            @if($order->status === 'pending')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Pendiente
                                </span>
                            @elseif($order->status === 'processing')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    En proceso
                                </span>
                            @elseif($order->status === 'completed')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Completado
                                </span>
                            @elseif($order->status === 'cancelled')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Cancelado
                                </span>
                            @endif
                        </p>
                    </div>
                    
                    <div>
                        <p class="font-medium text-gray-700 mb-1">Método de pago</p>
                        <p class="text-gray-600">{{ $order->paymentMethod->name }}</p>
                    </div>
                    
                    <div>
                        <p class="font-medium text-gray-700 mb-1">Total</p>
                        <p class="text-gray-600">{{ number_format($order->total, 2) }} €</p>
                    </div>
                </div>
                
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="font-semibold text-gray-700 mb-3">Artículos del pedido</h3>
                    
                    <div class="space-y-4">
                        @foreach($order->items as $item)
                            <div class="flex justify-between">
                                <div>
                                    <p class="font-medium">{{ $item->product->name }}</p>
                                    <p class="text-sm text-gray-600">Cantidad: {{ $item->quantity }}</p>
                                </div>
                                <p class="text-gray-600">{{ number_format($item->subtotal, 2) }} €</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mb-8">
            <h3 class="text-xl font-semibold mb-4">Dirección de envío</h3>
            
            <div class="bg-gray-50 rounded-lg p-6">
                <p class="font-medium">{{ $order->shippingAddress->name }}</p>
                <p>{{ $order->shippingAddress->address_line1 }}</p>
                <!-- resources/views/checkout/success.blade.php (continuación) -->
                @if($order->shippingAddress->address_line2)
                    <p>{{ $order->shippingAddress->address_line2 }}</p>
                @endif
                <p>{{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }} {{ $order->shippingAddress->postal_code }}</p>
                <p>{{ $order->shippingAddress->country }}</p>
                <p>{{ $order->shippingAddress->phone }}</p>
            </div>
        </div>
        
        <div class="border-t border-gray-200 pt-8 text-center">
            <p class="mb-4 text-gray-600">Hemos enviado un correo electrónico con los detalles de tu pedido a tu dirección de correo electrónico.</p>
            
            <div class="flex flex-col sm:flex-row justify-center space-y-3 sm:space-y-0 sm:space-x-4">
                <a href="{{ route('orders.show', $order->id) }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-blue-600 hover:bg-blue-700">
                    Ver detalles del pedido
                </a>
                <a href="{{ route('home') }}" class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 rounded-md shadow-sm text-base font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Volver a la tienda
                </a>
            </div>
        </div>
    </div>
</div>
@endsection