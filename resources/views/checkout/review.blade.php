<!-- resources/views/checkout/review.blade.php -->
@extends('layouts.app')

@section('title', 'Revisar Pedido')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row gap-8">
        <div class="md:w-2/3">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-bold mb-6">Revisar pedido</h2>
                
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-3">Dirección de envío</h3>
                    <div class="border rounded-lg p-4 text-gray-600">
                        <p class="font-medium text-gray-800">{{ $checkoutData['shipping_address']->name }}</p>
                        <p>{{ $checkoutData['shipping_address']->address_line1 }}</p>
                        @if($checkoutData['shipping_address']->address_line2)
                            <p>{{ $checkoutData['shipping_address']->address_line2 }}</p>
                        @endif
                        <p>{{ $checkoutData['shipping_address']->city }}, {{ $checkoutData['shipping_address']->state }} {{ $checkoutData['shipping_address']->postal_code }}</p>
                        <p>{{ $checkoutData['shipping_address']->country }}</p>
                        <p>{{ $checkoutData['shipping_address']->phone }}</p>
                    </div>
                </div>
                
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-3">Dirección de facturación</h3>
                    <div class="border rounded-lg p-4 text-gray-600">
                        <p class="font-medium text-gray-800">{{ $checkoutData['billing_address']->name }}</p>
                        <p>{{ $checkoutData['billing_address']->address_line1 }}</p>
                        @if($checkoutData['billing_address']->address_line2)
                            <p>{{ $checkoutData['billing_address']->address_line2 }}</p>
                        @endif
                        <p>{{ $checkoutData['billing_address']->city }}, {{ $checkoutData['billing_address']->state }} {{ $checkoutData['billing_address']->postal_code }}</p>
                        <p>{{ $checkoutData['billing_address']->country }}</p>
                        <p>{{ $checkoutData['billing_address']->phone }}</p>
                    </div>
                </div>
                
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-3">Método de pago</h3>
                    <div class="border rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <span class="font-medium">{{ $checkoutData['payment_method']->name }}</span>
                            @if($checkoutData['payment_method']->icon)
                                <img src="{{ asset($checkoutData['payment_method']->icon) }}" alt="{{ $checkoutData['payment_method']->name }}" class="h-8">
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-3">Productos</h3>
                    
                    <div class="border rounded-lg divide-y">
                        @foreach($cart->items as $item)
                            <div class="p-4 flex items-center">
                                <div class="w-16 h-16 flex-shrink-0">
                                    @if($item->product->getFirstMediaUrl('products'))
                                        <img src="{{ $item->product->getFirstMediaUrl('products', 'thumb') }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover rounded">
                                    @else
                                        <div class="w-full h-full bg-gray-200 rounded flex items-center justify-center text-gray-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4 flex-1">
                                    <h4 class="font-medium">{{ $item->product->name }}</h4>
                                    <p class="text-gray-600 text-sm">{{ $item->product->short_description }}</p>
                                    @if($item->options)
                                        <div class="mt-1 text-sm text-gray-600">
                                            @foreach($item->options as $key => $value)
                                                <span class="inline-block mr-2">{{ $key }}: {{ $value }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4 text-right">
                                    <p class="font-medium">{{ number_format($item->price, 2) }} €</p>
                                    <p class="text-gray-600">x {{ $item->quantity }}</p>
                                    <p class="font-medium mt-1">{{ number_format($item->subtotal, 2) }} €</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <form action="{{ route('checkout.complete') }}" method="POST">
                    @csrf
                    
                    <div class="mb-6">
                        <label for="notes" class="block text-gray-700 font-medium mb-2">Notas del pedido (opcional)</label>
                        <textarea id="notes" name="notes" rows="3" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Instrucciones especiales para el pedido"></textarea>
                    </div>
                    
                    <div class="mt-8 flex space-x-4">
                        <a href="{{ route('checkout.payment') }}" class="py-3 px-6 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                            Volver al método de pago
                        </a>
                        <button type="submit" class="flex-1 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            Confirmar pedido
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="md:w-1/3">
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-6">
                <h2 class="text-xl font-bold mb-4">Resumen del pedido</h2>
                
                <div class="border-t border-gray-200 pt-4 mb-4">
                    <div class="flex justify-between mb-2">
                        <span>Subtotal</span>
                        <span>{{ number_format($cart->subtotal, 2) }} €</span>
                    </div>
                    
                    @if(isset($cart->tax) && $cart->tax > 0)
                        <div class="flex justify-between mb-2">
                            <span>Impuestos</span>
                            <span>{{ number_format($cart->tax, 2) }} €</span>
                        </div>
                    @endif
                    
                    @if(isset($cart->shipping_cost) && $cart->shipping_cost > 0)
                        <div class="flex justify-between mb-2">
                            <span>Gastos de envío</span>
                            <span>{{ number_format($cart->shipping_cost, 2) }} €</span>
                        </div>
                    @endif
                    
                    @if(isset($cart->discount) && $cart->discount > 0)
                        <div class="flex justify-between mb-2 text-green-600">
                            <span>Descuento</span>
                            <span>-{{ number_format($cart->discount, 2) }} €</span>
                        </div>
                    @endif
                </div>
                
                <div class="border-t border-gray-200 pt-4">
                    <div class="flex justify-between font-bold text-lg">
                        <span>Total</span>
                        <span>{{ number_format($cart->total, 2) }} €</span>
                    </div>
                </div>
                
                <div class="mt-6 text-sm text-gray-600">
                    <p>Al confirmar el pedido, aceptas nuestros <a href="{{ route('terms') }}" class="text-blue-600 hover:underline">Términos y Condiciones</a> y nuestra <a href="{{ route('privacy') }}" class="text-blue-600 hover:underline">Política de Privacidad</a>.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection