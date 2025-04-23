<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Shipment;

class ShipmentUpdate extends Notification
{
    use Queueable;

    protected $shipment;

    /**
     * Create a new notification instance.
     *
     * @param Shipment $shipment
     * @return void
     */
    public function __construct(Shipment $shipment)
    {
        $this->shipment = $shipment;
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
     */
    public function toMail(object $notifiable)
    {
        $url = url('/account/orders/' . $this->shipment->order_id);
        $orderNumber = $this->shipment->order->order_number;

        return (new MailMessage)
            ->subject('Actualización de envío para pedido #' . $orderNumber)
            ->greeting('¡Actualización de tu pedido!')
            ->line('El envío de tu pedido #' . $orderNumber . ' ha sido actualizado.')
            ->line('Estado actual: ' . $this->shipment->status)
            ->when($this->shipment->tracking_number, function ($message) {
                return $message->line('Número de seguimiento: ' . $this->shipment->tracking_number);
            })
            ->action('Ver detalles del pedido', $url)
            ->line('Gracias por comprar con nosotros.');
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
            'order_id' => $this->shipment->order_id,
            'order_number' => $this->shipment->order->order_number,
            'shipment_id' => $this->shipment->id,
            'status' => $this->shipment->status,
            'tracking_number' => $this->shipment->tracking_number,
            'message' => 'El envío de tu pedido #' . $this->shipment->order->order_number . ' ha sido actualizado a: ' . $this->shipment->status
        ];
    }
}
