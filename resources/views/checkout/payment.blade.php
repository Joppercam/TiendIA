@extends('layouts.app')

@section('title', 'Seleccionar Método de Pago')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Seleccionar Método de Pago</h1>
        <p class="text-gray-600">Orden #{{ $order->order_number }}</p>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Resumen de la Orden</h2>
        <div class="border-t border-gray-200 pt-4">
            <div class="flex justify-between mb-2">
                <span>Subtotal</span>
                <span>${{ number_format($order->subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between mb-2">
                <span>IVA (19%)</span>
                <span>${{ number_format($order->tax, 0, ',', '.') }}</span>
            </div>
            @if($order->shipping_cost > 0)
            <div class="flex justify-between mb-2">
                <span>Envío</span>
                <span>${{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
            </div>
            @endif
            @if($order->discount > 0)
            <div class="flex justify-between mb-2">
                <span>Descuento</span>
                <span>-${{ number_format($order->discount, 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="flex justify-between font-bold border-t border-gray-200 pt-2 mt-2">
                <span>Total</span>
                <span>${{ number_format($order->total, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold mb-4">Métodos de Pago Disponibles</h2>
        
        @if($paymentGateways->isEmpty())
            <div class="bg-yellow-50 p-4 rounded-md">
                <p class="text-yellow-700">No hay métodos de pago disponibles en este momento. Por favor, contacte con soporte.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($paymentGateways as $gateway)
                    <div class="border border-gray-200 rounded-md p-4 hover:border-indigo-300 transition-colors">
                        <form action="{{ route('checkout.payment.process', $order->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="gateway" value="{{ $gateway->code }}">
                            
                            <div class="flex items-center mb-4">
                                @if($gateway->logo)
                                    <img src="{{ asset('storage/' . $gateway->logo) }}" alt="{{ $gateway->name }}" class="h-10 mr-3">
                                @endif
                                <div>
                                    <h3 class="font-semibold">{{ $gateway->name }}</h3>
                                    @if($gateway->description)
                                        <p class="text-sm text-gray-600">{{ $gateway->description }}</p>
                                    @endif
                                </div>
                            </div>
                            
                            @if($gateway->code === 'bank_transfer')
                                <div class="mb-4">
                                    <p class="text-gray-700 text-sm">{{ $gateway->instructions }}</p>
                                </div>
                            @endif
                            
                            <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded hover:bg-indigo-700 transition-colors">
                                Pagar con {{ $gateway->name }}
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection