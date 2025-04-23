<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Review;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
        $this->middleware('auth');
    }

    /**
     * Display a listing of the user's orders.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Order::where('user_id', Auth::id())
            ->with(['status', 'items']);

        // Filter by status if provided
        if ($request->has('status')) {
            $query->whereHas('status', function ($q) use ($request) {
                $q->where('slug', $request->status);
            });
        }

        // Filter by order number if provided
        if ($request->has('order_number')) {
            $query->where('order_number', 'like', '%' . $request->order_number . '%');
        }

        // Sort orders
        $sortField = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $orders = $query->paginate(10)->withQueryString();

        return view('shop.orders.index', compact('orders'));
    }

    /**
     * Display the specified order details.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        // Check if the order belongs to the authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $order->load([
            'items.product', 
            'status', 
            'address', 
            'paymentMethod', 
            'deliveryMethod',
            'shipment'
        ]);

        return view('shop.orders.show', compact('order'));
    }

    /**
     * Track the shipment for the specified order.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function track(Order $order)
    {
        // Check if the order belongs to the authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $order->load('shipment');

        // Check if the order has a shipment
        if (!$order->shipment) {
            return back()->with('error', 'Este pedido aún no tiene información de envío.');
        }

        return view('shop.orders.track', compact('order'));
    }

    /**
     * Display a form to write a review for purchased products.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function review(Order $order)
    {
        // Check if the order belongs to the authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Check if the order is delivered
        if ($order->status->slug !== 'delivered') {
            return back()->with('error', 'Solo puedes dejar reseñas para pedidos entregados.');
        }

        $order->load('items.product');

        // Get products that haven't been reviewed yet
        $reviewedProductIds = Review::where('user_id', Auth::id())
            ->whereIn('product_id', $order->items->pluck('product_id'))
            ->pluck('product_id')
            ->toArray();

        $pendingReviews = $order->items->filter(function ($item) use ($reviewedProductIds) {
            return $item->product && !in_array($item->product_id, $reviewedProductIds);
        });

        return view('shop.orders.review', compact('order', 'pendingReviews'));
    }

    /**
     * Store a new review for a product.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function storeReview(Request $request, Order $order)
    {
        // Check if the order belongs to the authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:1000',
            'title' => 'required|string|max:255',
        ]);

        // Check if product is in the order
        $productInOrder = $order->items->where('product_id', $request->product_id)->first();
        if (!$productInOrder) {
            return back()->with('error', 'Solo puedes dejar reseñas para productos que has comprado en este pedido.');
        }

        // Check if user already reviewed this product
        $existingReview = Review::where('user_id', Auth::id())
            ->where('product_id', $request->product_id)
            ->first();

        if ($existingReview) {
            return back()->with('error', 'Ya has dejado una reseña para este producto.');
        }

        // Create the review
        $review = new Review([
            'product_id' => $request->product_id,
            'user_id' => Auth::id(),
            'order_id' => $order->id,
            'rating' => $request->rating,
            'title' => $request->title,
            'comment' => $request->comment,
        ]);

        $review->save();

        return back()->with('success', '¡Gracias por tu reseña!');
    }

    /**
     * Cancel the specified order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function cancel(Request $request, Order $order)
    {
        // Check if the order belongs to the authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'reason' => 'required|string|max:1000'
        ]);

        // Check if the order can be cancelled (only pending or processing orders)
        $cancelableStatuses = ['pending', 'processing', 'payment_pending'];
        if (!in_array($order->status->slug, $cancelableStatuses)) {
            return back()->with('error', 'Este pedido no puede ser cancelado debido a su estado actual.');
        }

        // Cancel the order using the service
        $result = $this->orderService->cancelOrder($order, $request->reason);

        if ($result) {
            return redirect()->route('shop.orders.show', $order)
                ->with('success', 'Tu pedido ha sido cancelado correctamente.');
        } else {
            return back()->with('error', 'No se pudo cancelar el pedido. Por favor, contacta con servicio al cliente.');
        }
    }

    /**
     * Request a return/refund for the specified order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function requestReturn(Request $request, Order $order)
    {
        // Check if the order belongs to the authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'reason' => 'required|string|max:1000',
            'items' => 'required|array',
            'items.*' => 'exists:order_items,id'
        ]);

        // Check if the order is eligible for return (delivered orders within return period)
        if ($order->status->slug !== 'delivered' || $order->delivered_at->diffInDays(now()) > 30) {
            return back()->with('error', 'Este pedido no es elegible para devolución.');
        }

        // Process the return request using the service
        $result = $this->orderService->createReturnRequest($order, $request->items, $request->reason);

        if ($result) {
            return redirect()->route('shop.orders.show', $order)
                ->with('success', 'Tu solicitud de devolución ha sido enviada. Te contactaremos pronto.');
        } else {
            return back()->with('error', 'No se pudo procesar la solicitud de devolución. Por favor, contacta con servicio al cliente.');
        }
    }

    /**
     * Download invoice for the order.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function downloadInvoice(Order $order)
    {
        // Check if the order belongs to the authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Check if the order has an invoice
        if (!$order->invoice) {
            // Generate invoice if not exists
            $invoice = $this->orderService->generateInvoice($order);
            
            if (!$invoice) {
                return back()->with('error', 'No se pudo generar la factura para este pedido.');
            }
        }

        // Return the invoice PDF for download
        return response()->download(
            storage_path('app/invoices/' . $order->invoice->file_name),
            'Factura-' . $order->order_number . '.pdf'
        );
    }

    /**
     * Reorder items from a previous order.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function reorder(Order $order)
    {
        // Check if the order belongs to the authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $order->load('items.product');
        
        // Add each item to the cart
        $addedItems = 0;
        $outOfStockItems = [];
        
        foreach ($order->items as $item) {
            // Skip if product no longer exists
            if (!$item->product) {
                continue;
            }
            
            // Check if product is in stock
            if ($item->product->quantity < 1) {
                $outOfStockItems[] = $item->product_name;
                continue;
            }
            
            // Add to cart
            $this->orderService->addProductToCart($item->product_id, $item->quantity, json_decode($item->options, true));
            $addedItems++;
        }
        
        if ($addedItems > 0) {
            $message = 'Productos añadidos al carrito.';
            
            if (!empty($outOfStockItems)) {
                $message .= ' Algunos productos no estaban disponibles: ' . implode(', ', $outOfStockItems);
            }
            
            return redirect()->route('cart.index')->with('success', $message);
        } else {
            return back()->with('error', 'No se pudieron añadir productos al carrito. Los productos podrían no estar disponibles.');
        }
    }
}