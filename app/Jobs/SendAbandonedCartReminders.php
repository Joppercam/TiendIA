<?php

namespace App\Jobs;

use App\Models\Cart;
use App\Notifications\AbandonedCartNotification;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendAbandonedCartReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        // Encontrar carritos abandonados de usuarios registrados
        // Consideramos abandonado un carrito que tiene al menos 1 día de inactividad
        // pero menos de 3 días, y que contiene al menos un producto
        $abandonedCarts = Cart::where('is_guest', false)
                            ->whereNotNull('user_id')
                            ->where('updated_at', '<', Carbon::now()->subDay())
                            ->where('updated_at', '>', Carbon::now()->subDays(3))
                            ->whereHas('items')
                            ->with(['user', 'items.product'])
                            ->get();

        foreach ($abandonedCarts as $cart) {
            if ($cart->user) {
                // Enviar notificación de carrito abandonado
                $cart->user->notify(new AbandonedCartNotification($cart));
                
                // Actualizar la fecha de recordatorio
                $cart->reminded_at = Carbon::now();
                $cart->save();
            }
        }
    }
}