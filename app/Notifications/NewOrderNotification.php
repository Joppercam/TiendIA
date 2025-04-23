<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification implements ShouldQueue
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
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = url('/admin/orders/' . $this->order->id);
        
        return (new MailMessage)
            ->subject('Nuevo Pedido: #' . $this->order->order_number)
            ->greeting('Hola ' . $notifiable->name)
            ->line('Se ha recibido un nuevo pedido en la tienda.')
            ->line('Número de pedido: ' . $this->order->order_number)
            ->line('Cliente: ' . ($this->order->user ? $this->order->user->name : $this->order->guest_name))
            ->line('Total: ' . $this->order->currency . ' ' . number_format($this->order->total, 2))
            ->action('Ver Detalles del Pedido', $url)
            ->line('Gracias por utilizar nuestra aplicación.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'total' => $this->order->total,
            'customer' => $this->order->user ? $this->order->user->name : $this->order->guest_name,
            'message' => 'Nuevo pedido recibido: #' . $this->order->order_number
        ];
    }
}