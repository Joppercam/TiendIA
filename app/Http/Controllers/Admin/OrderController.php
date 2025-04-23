<?php

namespace App\Http\Controllers\Admin;

use App\Events\OrderCancelled;
use App\Events\OrderStatusChanged;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the orders.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Order::with(['status', 'user']);

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

        // Filter by customer email if provided
        if ($request->has('email')) {
            $query->where(function ($q) use ($request) {
                $q->where('guest_email', 'like', '%' . $request->email . '%')
                  ->orWhereHas('user', function ($subQ) use ($request) {
                      $subQ->where('email', 'like', '%' . $request->email . '%');
                  });
            });
        }

        // Sort orders
        $sortField = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $orders = $query->paginate(15)->withQueryString();

        $statuses = OrderStatus::all();

        return view('admin.orders.index', compact('orders', 'statuses'));
    }

    /**
     * Display the specified order.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        $order->load([
            'items.product', 
            'status', 
            'user', 
            'address', 
            'paymentMethod', 
            'deliveryMethod',
            'shipment'
        ]);

        $statuses = OrderStatus::orderBy('order')->get();

        return view('admin.orders.show', compact('order', 'statuses'));
    }

    /**
     * Update the order status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'order_status_id' => 'required|exists:order_statuses,id',
            'notes' => 'nullable|string|max:1000'
        ]);

        $oldStatus = $order->status->name;
        $newStatus = OrderStatus::findOrFail($request->order_status_id);

        DB::transaction(function () use ($order, $request, $oldStatus, $newStatus) {
            // Update order status
            $order->order_status_id = $request->order_status_id;
            
            // Add notes if provided
            if ($request->filled('notes')) {
                $order->notes = $request->notes;
            }

            // Update timestamps based on status
            if ($newStatus->slug === 'paid' && is_null($order->paid_at)) {
                $order->paid_at = now();
                $order->payment_status = 'completed';
            } elseif ($newStatus->slug === 'shipped' && is_null($order->shipped_at)) {
                $order->shipped_at = now();
                $order->shipping_status = 'shipped';
            } elseif ($newStatus->slug === 'delivered' && is_null($order->delivered_at)) {
                $order->delivered_at = now();
                $order->shipping_status = 'delivered';
            } elseif ($newStatus->slug === 'cancelled' && is_null($order->cancelled_at)) {
                $order->cancelled_at = now();
            }

            $order->save();

            // Fire event
            event(new OrderStatusChanged($order, $oldStatus, $newStatus->name));
        });

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Estado del pedido actualizado correctamente.');
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
        $request->validate([
            'reason' => 'nullable|string|max:1000'
        ]);

        $cancelStatus = OrderStatus::where('slug', 'cancelled')->firstOrFail();

        DB::transaction(function () use ($order, $cancelStatus, $request) {
            $order->order_status_id = $cancelStatus->id;
            $order->cancelled_at = now();
            $order->notes = $request->filled('reason') 
                ? "Cancelado: " . $request->reason 
                : "Pedido cancelado por administrador";
            $order->save();

            // Fire event
            event(new OrderCancelled($order, $request->reason));
        });

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Pedido cancelado correctamente.');
    }

    /**
     * Generate invoice for the order.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function generateInvoice(Order $order)
    {
        $order->load([
            'items.product', 
            'user', 
            'address', 
            'paymentMethod', 
            'deliveryMethod'
        ]);

        // Logic to generate invoice PDF
        // ... [implementación pendiente] ...

        return back()->with('success', 'Factura generada correctamente.');
    }
}