<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Mostrar el carrito de compras.
     */
    public function index()
    {
        $cart = $this->cartService->getCart();
        return view('cart.index', compact('cart'));
    }

    /**
     * Añadir un producto al carrito.
     */
    public function addItem(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'options' => 'sometimes|array',
        ]);

        try {
            $product = Product::findOrFail($request->product_id);
            $this->cartService->addToCart(
                $product, 
                $request->quantity, 
                $request->options ?? []
            );

            return redirect()->back()->with('success', 'Producto añadido al carrito correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Actualizar la cantidad de un producto en el carrito.
     */
    public function updateItem(Request $request)
    {
        $request->validate([
            'cart_item_id' => 'required|exists:cart_items,id',
            'quantity' => 'required|integer|min:0',
        ]);

        try {
            $this->cartService->updateQuantity(
                $request->cart_item_id, 
                $request->quantity
            );

            return redirect()->back()->with('success', 'Carrito actualizado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Eliminar un producto del carrito.
     */
    public function removeItem(Request $request)
    {
        $request->validate([
            'cart_item_id' => 'required|exists:cart_items,id',
        ]);

        try {
            $this->cartService->removeFromCart($request->cart_item_id);
            return redirect()->back()->with('success', 'Producto eliminado del carrito.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Vaciar el carrito.
     */
    public function clear()
    {
        try {
            $this->cartService->clearCart();
            return redirect()->back()->with('success', 'El carrito ha sido vaciado.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Guardar carrito para más tarde (convertir en lista de deseos).
     */
    public function saveForLater(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        try {
            $product = Product::findOrFail($request->product_id);
            $this->cartService->addToWishList($product);
            
            // Si también queremos eliminarlo del carrito
            if ($request->has('cart_item_id')) {
                $this->cartService->removeFromCart($request->cart_item_id);
            }

            return redirect()->back()->with('success', 'Producto guardado en tu lista de deseos.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}