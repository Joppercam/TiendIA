<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Notifications\OrderConfirmation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendOrderConfirmation implements ShouldQueue
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
        // Enviar confirmación al usuario
        if ($event->order->user) {
            $event->order->user->notify(new OrderConfirmation($event->order));
        }
        
        // Enviar notificación al administrador
        // Esto se podría implementar con una notificación separada
    }
}