<?php

namespace App\Console\Commands;

use App\Models\Cart;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ClearAbandonedCarts extends Command
{
    protected $signature = 'carts:clear-abandoned {--days=30 : Días de antigüedad}';
    protected $description = 'Limpia los carritos abandonados basados en su antigüedad';

    public function handle()
    {
        $days = $this->option('days');
        $date = Carbon::now()->subDays($days);
        
        $guestCarts = Cart::where('is_guest', true)
                        ->where('updated_at', '<', $date)
                        ->get();
                        
        $userCarts = Cart::where('is_guest', false)
                        ->whereNotNull('user_id')
                        ->where('updated_at', '<', $date)
                        ->get();
        
        $totalGuestCarts = $guestCarts->count();
        $totalUserCarts = $userCarts->count();
        
        // Eliminar los carritos de invitados
        foreach ($guestCarts as $cart) {
            $cart->items()->delete();
            $cart->delete();
        }
        
        // Eliminar los carritos de usuarios (solo los inactivos por mucho tiempo)
        foreach ($userCarts as $cart) {
            $cart->items()->delete();
            $cart->delete();
        }
        
        $this->info("Se han eliminado {$totalGuestCarts} carritos de invitados y {$totalUserCarts} carritos de usuarios con más de {$days} días de inactividad.");
        
        return 0;
    }
}