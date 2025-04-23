<?php

namespace App\Listeners;

use App\Events\OrderCancelled;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AdjustInventoryAfterCancel implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param  \App\Events\OrderCancelled  $event
     * @return void
     */
    public function handle(OrderCancelled $event)
    {
        $order = $event->order;
        
        // Restore inventory quantities for each item
        foreach ($order->items as $item) {
            if ($item->product && $item->product->manage_stock) {
                $item->product->increment('quantity', $item->quantity);
            }
        }
    }
}