<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\WishList;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartService
{
    /**
     * Obtener el carrito actual del usuario o crear uno nuevo si no existe.
     */
    public function getCart()
    {
        if (Auth::check()) {
            // Usuario autenticado
            $cart = Cart::where('user_id', Auth::id())
                    ->where('is_guest', false)
                    ->with('items.product')
                    ->first();

            if (!$cart) {
                // Verificar si hay un carrito de invitado con elementos
                $sessionId = Session::getId();
                $guestCart = Cart::where('session_id', $sessionId)
                            ->where('is_guest', true)
                            ->with('items.product')
                            ->first();

                if ($guestCart) {
                    // Migrar carrito de invitado a usuario autenticado
                    $guestCart->user_id = Auth::id();
                    $guestCart->is_guest = false;
                    $guestCart->save();
                    return $guestCart;
                }

                // Crear nuevo carrito para usuario autenticado
                return Cart::create([
                    'user_id' => Auth::id(),
                    'is_guest' => false,
                ]);
            }

            return $cart;
        } else {
            // Usuario invitado
            $sessionId = Session::getId();
            $cart = Cart::where('session_id', $sessionId)
                    ->where('is_guest', true)
                    ->with('items.product')
                    ->first();

            if (!$cart) {
                // Crear nuevo carrito para invitado
                return Cart::create([
                    'session_id' => $sessionId,
                    'is_guest' => true,
                ]);
            }

            return $cart;
        }
    }

    /**
     * Añadir un producto al carrito.
     */
    public function addToCart(Product $product, int $quantity = 1, array $options = [])
    {
        $cart = $this->getCart();
        
        // Verificar si el producto ya está en el carrito
        $cartItem = CartItem::where('cart_id', $cart->id)
                  ->where('product_id', $product->id)
                  ->first();

        // Verificar disponibilidad en inventario
        $inventoryService = app(InventoryService::class);
        if (!$inventoryService->checkAvailability($product->id, $quantity)) {
            throw new \Exception('No hay suficiente stock disponible.');
        }

        if ($cartItem) {
            // Actualizar cantidad si ya existe
            $cartItem->quantity += $quantity;
            $cartItem->save();
        } else {
            // Crear nuevo item
            $cartItem = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'price' => $product->special_price ?? $product->price,
                'options' => $options,
            ]);
        }

        return $cartItem;
    }

    /**
     * Actualizar la cantidad de un producto en el carrito.
     */
    public function updateQuantity(int $cartItemId, int $quantity)
    {
        $cartItem = CartItem::findOrFail($cartItemId);
        
        // Verificar que el carrito pertenece al usuario actual
        $cart = $this->getCart();
        if ($cartItem->cart_id !== $cart->id) {
            throw new \Exception('No tienes permiso para modificar este carrito.');
        }

        // Verificar disponibilidad en inventario
        $inventoryService = app(InventoryService::class);
        if (!$inventoryService->checkAvailability($cartItem->product_id, $quantity)) {
            throw new \Exception('No hay suficiente stock disponible.');
        }

        if ($quantity <= 0) {
            // Eliminar el item si la cantidad es 0 o negativa
            $cartItem->delete();
            return null;
        }

        $cartItem->quantity = $quantity;
        $cartItem->save();

        return $cartItem;
    }

    /**
     * Eliminar un producto del carrito.
     */
    public function removeFromCart(int $cartItemId)
    {
        $cartItem = CartItem::findOrFail($cartItemId);
        
        // Verificar que el carrito pertenece al usuario actual
        $cart = $this->getCart();
        if ($cartItem->cart_id !== $cart->id) {
            throw new \Exception('No tienes permiso para modificar este carrito.');
        }

        $cartItem->delete();
        return true;
    }

    /**
     * Vaciar el carrito.
     */
    public function clearCart()
    {
        $cart = $this->getCart();
        CartItem::where('cart_id', $cart->id)->delete();
        return true;
    }

    /**
     * Añadir un producto a la lista de deseos.
     */
    public function addToWishList(Product $product)
    {
        if (!Auth::check()) {
            throw new \Exception('Debes iniciar sesión para añadir productos a tu lista de deseos.');
        }

        $wishListItem = WishList::firstOrCreate([
            'user_id' => Auth::id(),
            'product_id' => $product->id,
        ]);

        return $wishListItem;
    }

    /**
     * Eliminar un producto de la lista de deseos.
     */
    public function removeFromWishList(int $productId)
    {
        if (!Auth::check()) {
            throw new \Exception('Debes iniciar sesión para modificar tu lista de deseos.');
        }

        WishList::where('user_id', Auth::id())
              ->where('product_id', $productId)
              ->delete();

        return true;
    }

    /**
     * Obtener la lista de deseos del usuario.
     */
    public function getWishList()
    {
        if (!Auth::check()) {
            return collect();
        }

        return WishList::where('user_id', Auth::id())
                    ->with('product')
                    ->get();
    }

    /**
     * Transferir un producto de la lista de deseos al carrito.
     */
    public function moveToCart(int $productId, int $quantity = 1, array $options = [])
    {
        if (!Auth::check()) {
            throw new \Exception('Debes iniciar sesión para usar esta función.');
        }

        $product = Product::findOrFail($productId);
        
        // Añadir al carrito
        $this->addToCart($product, $quantity, $options);
        
        // Remover de la lista de deseos
        $this->removeFromWishList($productId);

        return true;
    }
}