<?php

namespace App\Notifications;

use App\Models\Cart;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AbandonedCartNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $cart;

    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = route('cart.index');

        return (new MailMessage)
                    ->subject('¿Olvidaste algo en tu carrito?')
                    ->greeting('Hola ' . $notifiable->name . ',')
                    ->line('Notamos que tienes productos en tu carrito de compras.')
                    ->line('¡No pierdas la oportunidad de completar tu compra!')
                    ->action('Retomar mi compra', $url)
                    ->line('Tu carrito contiene ' . $this->cart->items->count() . ' productos:')
                    ->line($this->getProductsList())
                    ->line('Gracias por comprar con nosotros en TiendIA.');
    }

    protected function getProductsList()
    {
        $list = '';
        foreach ($this->cart->items as $item) {
            $list .= "- {$item->product->name} (x{$item->quantity})\n";
        }
        return $list;
    }
}