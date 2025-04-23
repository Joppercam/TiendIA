<?php

namespace App\Http\Middleware;

use App\Services\CartService;
use Closure;
use Illuminate\Http\Request;

class CheckoutMiddleware
{
    protected $cartService;
    
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }
    
    public function handle(Request $request, Closure $next)
    {
        $cart = $this->cartService->getCart();
        
        // Check if cart exists and has items
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Tu carrito está vacío, no puedes proceder al checkout.');
        }
        
        // Check if all items in cart are available
        foreach ($cart->items as $item) {
            if (!$item->product || !$item->product->isAvailable() || $item->quantity > $item->product->quantity) {
                return redirect()->route('cart.index')
                    ->with('error', "El producto '{$item->product->name}' no está disponible o no tiene suficiente stock.");
            }
        }
        
        return $next($request);
    }
}