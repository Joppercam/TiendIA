<?php

namespace App\Http\Controllers\Cart;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = session()->get('cart', []);
        $productIds = array_keys($cartItems);
        
        $products = [];
        if (!empty($productIds)) {
            $products = Product::whereIn('id', $productIds)
                ->with(['category', 'images' => function($q) {
                    $q->where('is_primary', true);
                }])
                ->get()
                ->keyBy('id');
        }
        
        return view('cart.index', compact('cartItems', 'products'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $productId = $request->product_id;
        $quantity = $request->quantity;

        // Recuperar el carrito actual desde la sesión
        $cart = session()->get('cart', []);

        // Verificar si el producto ya está en el carrito
        if (isset($cart[$productId])) {
            $cart[$productId] += $quantity;
        } else {
            $cart[$productId] = $quantity;
        }

        // Guardar el carrito actualizado en la sesión
        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Producto añadido al carrito correctamente');
    }

    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $productId = $request->product_id;
        $quantity = $request->quantity;

        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            $cart[$productId] = $quantity;
            session()->put('cart', $cart);
        }

        return redirect()->back()->with('success', 'Carrito actualizado correctamente');
    }

    public function remove(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $productId = $request->product_id;
        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session()->put('cart', $cart);
        }

        return redirect()->back()->with('success', 'Producto eliminado del carrito correctamente');
    }

    public function clear()
    {
        session()->forget('cart');
        return redirect()->back()->with('success', 'Carrito vaciado correctamente');
    }
}