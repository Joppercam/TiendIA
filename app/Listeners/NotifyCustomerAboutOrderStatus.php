<?php

namespace App\Listeners;

use App\Events\OrderStatusChanged;
use App\Notifications\OrderStatusUpdate;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyCustomerAboutOrderStatus implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param  \App\Events\OrderStatusChanged  $event
     * @return void
     */
    public function handle(OrderStatusChanged $event)
    {
        $order = $event->order;
        
        // Only notify if the order belongs to a registered user
        if ($order->user) {
            $order->user->notify(new OrderStatusUpdate($order, $event->oldStatus, $event->newStatus));
        } else if ($order->guest_email) {
            // If it's a guest order, send notification via email
            \Notification::route('mail', $order->guest_email)
                ->notify(new OrderStatusUpdate($order, $event->oldStatus, $event->newStatus));
        }
    }
}