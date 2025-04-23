<?php

namespace App\Services;

use App\Events\OrderCancelled;
use App\Events\OrderPlaced;
use App\Events\OrderStatusChanged;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\ReturnRequest;
use App\Models\Cart;
use App\Models\Product;
use App\Notifications\OrderConfirmation;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * Create a new order from cart
     *
     * @param array $orderData
     * @param Cart $cart
     * @param int $userId
     * @return Order|null
     */
    public function createOrder(array $orderData, Cart $cart, $userId = null)
    {
        try {
            return DB::transaction(function () use ($orderData, $cart, $userId) {
                // Get default status for new orders
                $defaultStatus = OrderStatus::where('is_default', true)->first();
                if (!$defaultStatus) {
                    $defaultStatus = OrderStatus::where('slug', 'pending')->first();
                }

                // Create the order
                $order = new Order([
                    'user_id' => $userId,
                    'order_status_id' => $defaultStatus->id,
                    'order_number' => $this->generateOrderNumber(),
                    'subtotal' => $cart->getSubtotal(),
                    'tax' => $cart->getTax(),
                    'shipping_cost' => $orderData['shipping_cost'] ?? 0,
                    'discount' => $cart->getDiscount(),
                    'total' => $cart->getTotal() + ($orderData['shipping_cost'] ?? 0),
                    'currency' => config('app.currency', 'USD'),
                    'payment_method_id' => $orderData['payment_method_id'] ?? null,
                    'delivery_method_id' => $orderData['delivery_method_id'] ?? null,
                    'address_id' => $orderData['address_id'] ?? null,
                    'notes' => $orderData['notes'] ?? null,
                    'is_guest_checkout' => !$userId,
                    'guest_email' => $userId ? null : ($orderData['email'] ?? null),
                    'guest_name' => $userId ? null : ($orderData['name'] ?? null),
                ]);

                $order->save();

                // Create order items from cart items
                foreach ($cart->items as $cartItem) {
                    $product = $cartItem->product;
                    
                    $order->items()->create([
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'product_sku' => $product->sku,
                        'quantity' => $cartItem->quantity,
                        'price' => $cartItem->price,
                        'subtotal' => $cartItem->subtotal,
                        'options' => $cartItem->options
                    ]);

                    // Update inventory
                    if ($product->manage_stock) {
                        $product->decrement('quantity', $cartItem->quantity);
                    }
                }

                // Fire order placed event
                event(new OrderPlaced($order));

                // Notify customer
                if ($order->user) {
                    $order->user->notify(new OrderConfirmation($order));
                }

                return $order;
            });
        } catch (\Exception $e) {
            \Log::error('Error creating order: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate a unique order number
     *
     * @return string
     */
    protected function generateOrderNumber()
    {
        $prefix = 'ORD-';
        $timestamp = now()->format('YmdHis');
        $random = rand(100, 999);
        
        return $prefix . $timestamp . $random;
    }

    /**
     * Cancel an order
     *
     * @param Order $order
     * @param string $reason
     * @return bool
     */
    public function cancelOrder(Order $order, string $reason)
    {
        try {
            return DB::transaction(function () use ($order, $reason) {
                // Get cancelled status
                $cancelledStatus = OrderStatus::where('slug', 'cancelled')->first();
                if (!$cancelledStatus) {
                    throw new \Exception('Cancelled status not found');
                }

                // Update order status
                $oldStatus = $order->status->name;
                $order->order_status_id = $cancelledStatus->id;
                $order->cancelled_at = now();
                $order->notes = $order->notes . "\nCancelled: " . $reason;
                $order->save();

                // Restore inventory
                foreach ($order->items as $item) {
                    if ($item->product && $item->product->manage_stock) {
                        $item->product->increment('quantity', $item->quantity);
                    }
                }

                // Fire events
                event(new OrderStatusChanged($order, $oldStatus, $cancelledStatus->name));
                event(new OrderCancelled($order, $reason));

                return true;
            });
        } catch (\Exception $e) {
            \Log::error('Error cancelling order: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Create a return request for an order
     *
     * @param Order $order
     * @param array $items
     * @param string $reason
     * @return ReturnRequest|null
     */
    public function createReturnRequest(Order $order, array $items, string $reason)
    {
        try {
            return DB::transaction(function () use ($order, $items, $reason) {
                // Create return request
                $returnRequest = new ReturnRequest([
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                    'reason' => $reason,
                    'status' => 'pending'
                ]);

                $returnRequest->save();

                // Add items to return request
                foreach ($items as $itemId) {
                    $orderItem = $order->items()->find($itemId);
                    if ($orderItem) {
                        $returnRequest->items()->create([
                            'order_item_id' => $orderItem->id,
                            'quantity' => $orderItem->quantity,
                            'status' => 'pending'
                        ]);
                    }
                }

                return $returnRequest;
            });
        } catch (\Exception $e) {
            \Log::error('Error creating return request: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate an invoice for an order
     *
     * @param Order $order
     * @return mixed
     */
    public function generateInvoice(Order $order)
    {
        try {
            // Load order relations
            $order->load(['items', 'user', 'address']);
            
            // Create invoice filename
            $filename = 'invoice-' . $order->order_number . '.pdf';
            
            // Generate PDF using a PDF library
            // (Implementation depends on the PDF library you're using)
            // For example with Laravel DomPDF:
            $pdf = \PDF::loadView('pdfs.invoice', compact('order'));
            $pdf->save(storage_path('app/invoices/' . $filename));
            
            // Create invoice record
            $invoice = $order->invoice()->create([
                'invoice_number' => 'INV-' . $order->order_number,
                'file_name' => $filename,
                'issued_at' => now()
            ]);
            
            return $invoice;
        } catch (\Exception $e) {
            \Log::error('Error generating invoice: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Add a product to cart
     *
     * @param int $productId
     * @param int $quantity
     * @param array $options
     * @return bool
     */
    public function addProductToCart($productId, $quantity = 1, $options = [])
    {
        try {
            // Get product
            $product = Product::findOrFail($productId);
            
            // Check stock
            if ($product->manage_stock && $product->quantity < $quantity) {
                return false;
            }
            
            // Get or create cart
            $cartService = app(CartService::class);
            $cart = $cartService->getCart();
            
            // Add product to cart
            $cartService->addToCart($product, $quantity, $options);
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Error adding product to cart: ' . $e->getMessage());
            return false;
        }
    }
}