<?php

namespace App\Http\Middleware;

use App\Services\CartService;
use Closure;
use Illuminate\Http\Request;

class CartMiddleware
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Obtener el carrito y compartirlo con todas las vistas
        $cart = $this->cartService->getCart();
        view()->share('cart', $cart);
        
        // Obtener la cantidad total de elementos en el carrito para el icono de carrito en el header
        view()->share('cartItemCount', $cart->total_items);
        
        // Si el usuario está autenticado, compartir la lista de deseos
        if (auth()->check()) {
            $wishList = $this->cartService->getWishList();
            view()->share('wishListCount', $wishList->count());
        }

        return $next($request);
    }
}