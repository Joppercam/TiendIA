<!-- resources/views/checkout/guest.blade.php -->
@extends('layouts.app')

@section('title', 'Checkout como Invitado')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-2xl font-bold mb-6">Opciones de checkout</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="border rounded-lg p-6">
                    <h3 class="text-xl font-semibold mb-4">Ya tengo una cuenta</h3>
                    <p class="text-gray-600 mb-6">Si ya tienes una cuenta, inicia sesión para un proceso de checkout más rápido.</p>
                    
                    <form action="{{ route('login') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="email" class="block text-gray-700 font-medium mb-2">Correo electrónico</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label for="password" class="block text-gray-700 font-medium mb-2">Contraseña</label>
                            <input type="password" id="password" name="password" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" id="remember" name="remember" class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                            <label for="remember" class="ml-2 block text-gray-600">Recordarme</label>
                        </div>
                        
                        <div>
                            <button type="submit" class="w-full py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                Iniciar sesión
                            </button>
                        </div>
                        
                        <div class="text-center text-sm text-gray-600">
                            <a href="{{ route('password.request') }}" class="text-blue-600 hover:underline">¿Olvidaste tu contraseña?</a>
                        </div>
                    </form>
                </div>
                
                <div class="border rounded-lg p-6">
                    <h3 class="text-xl font-semibold mb-4">Soy un cliente nuevo</h3>
                    <p class="text-gray-600 mb-6">Crea una cuenta para un proceso de checkout más rápido y para guardar múltiples direcciones de envío.</p>
                    
                    <div class="space-y-6">
                        <a href="{{ route('register') }}" class="block w-full py-3 text-center bg-green-600 text-white rounded-lg hover:bg-green-700">
                            Crear una cuenta
                        </a>
                        
                        <div class="relative">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-300"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-2 bg-white text-gray-500">o</span>
                            </div>
                        </div>
                        
                        <a href="{{ route('checkout.index') }}" class="block w-full py-3 text-center border border-gray-300 rounded-lg hover:bg-gray-50">
                            Continuar como invitado
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold mb-4">Resumen del carrito</h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($cart->items as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0">
                                            @if($item->product->getFirstMediaUrl('products'))
                                                <img src="{{ $item->product->getFirstMediaUrl('products', 'thumb') }}" alt="{{ $item->product->name }}" class="h-10 w-10 rounded-full">
                                            @else
                                                <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $item->product->name }}</div>
                                            @if($item->options)
                                                <div class="text-sm text-gray-500">
                                                    @foreach($item->options as $key => $value)
                                                        <span class="mr-2">{{ $key }}: {{ $value }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ number_format($item->price, 2) }} €</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $item->quantity }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ number_format($item->subtotal, 2) }} €</div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50">
                            <td colspan="3" class="px-6 py-4 text-right font-medium">Subtotal</td>
                            <td class="px-6 py-4 font-medium">{{ number_format($cart->subtotal, 2) }} €</td>
                        </tr>
                        @if(isset($cart->tax) && $cart->tax > 0)
                            <tr class="bg-gray-50">
                                <td colspan="3" class="px-6 py-4 text-right font-medium">Impuestos</td>
                                <td class="px-6 py-4 font-medium">{{ number_format($cart->tax, 2) }} €</td>
                            </tr>
                        @endif
                        @if(isset($cart->shipping_cost) && $cart->shipping_cost > 0)
                            <tr class="bg-gray-50">
                                <td colspan="3" class="px-6 py-4 text-right font-medium">Gastos de envío</td>
                                <td class="px-6 py-4 font-medium">{{ number_format($cart->shipping_cost, 2) }} €</td>
                            </tr>
                        @endif
                        @if(isset($cart->discount) && $cart->discount > 0)
                            <tr class="bg-gray-50">
                                <td colspan="3" class="px-6 py-4 text-right font-medium text-green-600">Descuento</td>
                                <td class="px-6 py-4 font-medium text-green-600">-{{ number_format($cart->discount, 2) }} €</td>
                            </tr>
                        @endif
                        <tr class="bg-gray-100">
                            <td colspan="3" class="px-6 py-4 text-right font-bold">Total</td>
                            <td class="px-6 py-4 font-bold">{{ number_format($cart->total, 2) }} €</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection