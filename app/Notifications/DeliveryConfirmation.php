<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Order;

class DeliveryConfirmation extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;

    /**
     * Create a new notification instance.
     *
     * @param Order $order
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = url('/account/orders/' . $this->order->id . '/review');

        return (new MailMessage)
            ->subject('Pedido #' . $this->order->order_number . ' entregado')
            ->greeting('¡Tu pedido ha sido entregado!')
            ->line('Nos complace informarte que tu pedido #' . $this->order->order_number . ' ha sido entregado.')
            ->line('Esperamos que estés disfrutando de tu compra.')
            ->action('Dejar una reseña', $url)
            ->line('Gracias por confiar en nosotros.');
    }


    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'delivered_at' => $this->order->delivered_at,
            'message' => 'Tu pedido #' . $this->order->order_number . ' ha sido entregado con éxito.'
        ];
    }
}
