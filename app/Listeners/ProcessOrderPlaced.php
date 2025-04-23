<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Models\OrderStatus;
use App\Notifications\OrderConfirmation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ProcessOrderPlaced implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param  \App\Events\OrderPlaced  $event
     * @return void
     */
    public function handle(OrderPlaced $event)
    {
        // Process the order
        $order = $event->order;
        
        // If payment is pending, set appropriate status
        if ($order->payment_status === 'pending' && $order->paymentMethod && $order->paymentMethod->is_online) {
            $pendingPaymentStatus = OrderStatus::where('slug', 'payment_pending')->first();
            if ($pendingPaymentStatus) {
                $order->order_status_id = $pendingPaymentStatus->id;
                $order->save();
            }
        }
        
        // If payment is already completed, move to processing
        if ($order->payment_status === 'completed' || ($order->paymentMethod && !$order->paymentMethod->is_online)) {
            $processingStatus = OrderStatus::where('slug', 'processing')->first();
            if ($processingStatus) {
                $order->order_status_id = $processingStatus->id;
                $order->save();
            }
        }
        
        // Create records in other systems if needed
        // ...
    }
}