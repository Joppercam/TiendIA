<?php

namespace App\Listeners;

use App\Events\LowStockAlert;
use App\Models\User;
use App\Notifications\LowStockNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendLowStockNotification implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(LowStockAlert $event): void
    {
        // Obtener administradores y managers para notificar
        $admins = User::role(['admin', 'super-admin'])->get();
        
        foreach ($admins as $admin) {
            $admin->notify(new LowStockNotification($event->inventory));
        }
    }
}