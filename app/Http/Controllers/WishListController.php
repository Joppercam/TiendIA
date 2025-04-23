<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;

class WishListController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
        $this->middleware('auth');
    }

    /**
     * Mostrar la lista de deseos.
     */
    public function index()
    {
        $wishList = $this->cartService->getWishList();
        return view('cart.wish-list', compact('wishList'));
    }

    /**
     * Añadir un producto a la lista de deseos.
     */
    public function addItem(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        try {
            $product = Product::findOrFail($request->product_id);
            $this->cartService->addToWishList($product);

            return redirect()->back()->with('success', 'Producto añadido a tu lista de deseos.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Eliminar un producto de la lista de deseos.
     */
    public function removeItem(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        try {
            $this->cartService->removeFromWishList($request->product_id);
            return redirect()->back()->with('success', 'Producto eliminado de tu lista de deseos.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Mover un producto de la lista de deseos al carrito.
     */
    public function moveToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'sometimes|integer|min:1',
            'options' => 'sometimes|array',
        ]);

        try {
            $this->cartService->moveToCart(
                $request->product_id,
                $request->quantity ?? 1,
                $request->options ?? []
            );

            return redirect()->back()->with('success', 'Producto movido al carrito.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}