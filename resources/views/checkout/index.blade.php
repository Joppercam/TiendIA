<!-- resources/views/checkout/index.blade.php -->
@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row gap-8">
        <div class="md:w-2/3">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-bold mb-6">Dirección de envío</h2>
                
                <form action="{{ route('checkout.address') }}" method="POST">
                    @csrf
                    
                    @if($addresses->isNotEmpty())
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-3">Selecciona una dirección</h3>
                            
                            @foreach($addresses as $address)
                                <div class="mb-3 p-4 border rounded-lg @if($address->is_default) border-blue-500 @endif">
                                    <label class="flex items-start">
                                        <input type="radio" name="shipping_address_id" value="{{ $address->id }}" class="mt-1 mr-2" @if($address->is_default) checked @endif>
                                        <div>
                                            <p class="font-medium">{{ $address->name }}</p>
                                            <p>{{ $address->address_line1 }}</p>
                                            @if($address->address_line2)
                                                <p>{{ $address->address_line2 }}</p>
                                            @endif
                                            <p>{{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}</p>
                                            <p>{{ $address->country }}</p>
                                            <p>{{ $address->phone }}</p>
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-4">
                            <a href="{{ route('addresses.create') }}" class="text-blue-600 hover:underline">
                                + Añadir nueva dirección
                            </a>
                        </div>
                    @else
                         <!-- resources/views/checkout/index.blade.php (continuación) -->
                        <div class="mb-6">
                            <p class="text-gray-600 mb-4">No tienes direcciones guardadas. Por favor, crea una nueva dirección para continuar.</p>
                            
                            <a href="{{ route('addresses.create') }}" class="inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                Añadir nueva dirección
                            </a>
                        </div>
                    @endif
                    
                    <div class="mt-8">
                        <h3 class="text-lg font-semibold mb-3">Dirección de facturación</h3>
                        
                        <div class="mb-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="same_billing_address" value="1" class="mr-2" checked>
                                <span>Usar la misma dirección para facturación</span>
                            </label>
                        </div>
                        
                        <div id="billing-address-selection" class="hidden mt-4">
                            @if($addresses->isNotEmpty())
                                @foreach($addresses as $address)
                                    <div class="mb-3 p-4 border rounded-lg">
                                        <label class="flex items-start">
                                            <input type="radio" name="billing_address_id" value="{{ $address->id }}" class="mt-1 mr-2" @if($address->is_default) checked @endif>
                                            <div>
                                                <p class="font-medium">{{ $address->name }}</p>
                                                <p>{{ $address->address_line1 }}</p>
                                                @if($address->address_line2)
                                                    <p>{{ $address->address_line2 }}</p>
                                                @endif
                                                <p>{{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}</p>
                                                <p>{{ $address->country }}</p>
                                                <p>{{ $address->phone }}</p>
                                            </div>
                                        </label>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    
                    <div class="mt-8">
                        <button type="submit" class="w-full py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Continuar al método de pago
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="md:w-1/3">
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-6">
                <h2 class="text-xl font-bold mb-4">Resumen del pedido</h2>
                
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
    const sameBillingCheckbox = document.querySelector('input[name="same_billing_address"]');
    const billingAddressSelection = document.getElementById('billing-address-selection');
    
    sameBillingCheckbox.addEventListener('change', function() {
        if (this.checked) {
            billingAddressSelection.classList.add('hidden');
        } else {
            billingAddressSelection.classList.remove('hidden');
        }
    });
});
</script>
@endpush
@endsection