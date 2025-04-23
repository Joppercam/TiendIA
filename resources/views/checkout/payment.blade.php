<!-- resources/views/checkout/payment.blade.php -->
@extends('layouts.app')

@section('title', 'Método de Pago')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row gap-8">
        <div class="md:w-2/3">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-bold mb-6">Método de pago</h2>
                
                <form action="{{ route('checkout.process-payment') }}" method="POST">
                    @csrf
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-3">Selecciona un método de pago</h3>
                        
                        <div class="space-y-4">
                            @foreach($paymentMethods as $method)
                                <div class="border rounded-lg p-4 @if($loop->first) border-blue-500 @endif">
                                    <label class="flex items-center">
                                        <input type="radio" name="payment_method_id" value="{{ $method->id }}" class="mr-2" @if($loop->first) checked @endif>
                                        <div class="w-full">
                                            <div class="flex items-center justify-between">
                                                <span class="font-medium">{{ $method->name }}</span>
                                                @if($method->icon)
                                                    <img src="{{ asset($method->icon) }}" alt="{{ $method->name }}" class="h-8">
                                                @endif
                                            </div>
                                            @if($method->description)
                                                <p class="text-gray-600 mt-1">{{ $method->description }}</p>
                                            @endif
                                        </div>
                                    </label>
                                    
                                    @if($method->code === 'credit_card')
                                        <div class="mt-4 pt-4 border-t payment-details" id="credit-card-details">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-gray-700 mb-2">Número de tarjeta</label>
                                                    <input type="text" name="card_number" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="XXXX XXXX XXXX XXXX">
                                                </div>
                                                <div>
                                                    <label class="block text-gray-700 mb-2">Nombre en la tarjeta</label>
                                                    <input type="text" name="card_name" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                </div>
                                                <div>
                                                    <label class="block text-gray-700 mb-2">Fecha de expiración</label>
                                                    <input type="text" name="card_expiry" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="MM/AA">
                                                </div>
                                                <div>
                                                    <label class="block text-gray-700 mb-2">CVV</label>
                                                    <input type="text" name="card_cvv" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="XXX">
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if($method->code === 'bank_transfer' && $method->instructions)
                                        <div class="mt-4 pt-4 border-t payment-details" id="bank-transfer-details">
                                            <h4 class="font-medium mb-2">Instrucciones para la transferencia</h4>
                                            <div class="bg-gray-50 p-3 rounded-lg">
                                                {{ $method->instructions }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="mt-8 flex space-x-4">
                        <a href="{{ route('checkout.index') }}" class="py-3 px-6 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                            Volver a dirección
                        </a>
                        <button type="submit" class="flex-1 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Continuar a la revisión
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="md:w-1/3">
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-6">
                <h2 class="text-xl font-bold mb-4">Resumen del pedido</h2>
                
                <div class="mb-4">
                    <h3 class="font-medium text-gray-700 mb-2">Dirección de envío</h3>
                    <div class="text-gray-600">
                        <p>{{ $addresses['shipping_address']->name }}</p>
                        <p>{{ $addresses['shipping_address']->address_line1 }}</p>
                        @if($addresses['shipping_address']->address_line2)
                            <p>{{ $addresses['shipping_address']->address_line2 }}</p>
                        @endif
                        <p>{{ $addresses['shipping_address']->city }}, {{ $addresses['shipping_address']->state }} {{ $addresses['shipping_address']->postal_code }}</p>
                        <p>{{ $addresses['shipping_address']->country }}</p>
                        <p>{{ $addresses['shipping_address']->phone }}</p>
                    </div>
                </div>
                
                <div class="mb-4">
                    <h3 class="font-medium text-gray-700 mb-2">Productos</h3>
                    
                    <div class="space-y-3">
                        @foreach($cart->items as $item)
                            <div class="flex justify-between">
                                <div>
                                    <span class="font-medium">{{ $item->product->name }}</span>
                                    <span class="text-gray-600 ml-1">x {{ $item->quantity }}</span>
                                </div>
                                <span>{{ number_format($item->subtotal, 2) }} €</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                
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
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethodRadios = document.querySelectorAll('input[name="payment_method_id"]');
    const paymentDetails = document.querySelectorAll('.payment-details');
    
    // Hide all payment details initially except the first one
    paymentDetails.forEach(function(detail, index) {
        if (index !== 0) {
            detail.classList.add('hidden');
        }
    });
    
    paymentMethodRadios.forEach(function(radio) {
        radio.addEventListener('change', function() {
            // Hide all payment details
            paymentDetails.forEach(function(detail) {
                detail.classList.add('hidden');
            });
            
            // Show the payment details for the selected method
            const selectedMethod = document.querySelector(`input[name="payment_method_id"][value="${this.value}"]`).closest('.border').querySelector('.payment-details');
            if (selectedMethod) {
                selectedMethod.classList.remove('hidden');
            }
        });
    });
});
</script>
@endpush
@endsection