<?php

namespace App\Listeners;

use App\Events\OrderCancelled;
use App\Notifications\OrderCancellationNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyCustomerAboutCancel implements ShouldQueue
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
        
        // Notify customer about cancellation
        if ($order->user) {
            $order->user->notify(new OrderCancellationNotification($order, $event->reason));
        } else if ($order->guest_email) {
            // If it's a guest order, send notification via email
            \Notification::route('mail', $order->guest_email)
                ->notify(new OrderCancellationNotification($order, $event->reason));
        }
    }
}