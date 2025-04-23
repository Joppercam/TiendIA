<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusUpdate extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;
    protected $oldStatus;
    protected $newStatus;

    /**
     * Create a new notification instance.
     *
     * @param Order $order
     * @param string $oldStatus
     * @param string $newStatus
     * @return void
     */
    public function __construct(Order $order, string $oldStatus, string $newStatus)
    {
        $this->order = $order;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
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
        $url = url('/account/orders/' . $this->order->id);
        
        $mail = (new MailMessage)
            ->subject('Actualización del Pedido #' . $this->order->order_number)
            ->greeting('Hola ' . $notifiable->name)
            ->line('El estado de tu pedido ha cambiado de "' . $this->oldStatus . '" a "' . $this->newStatus . '".');
        
        // Agregar información específica según el nuevo estado
        if ($this->newStatus == 'Procesando') {
            $mail->line('Tu pedido está siendo preparado para envío.');
        } elseif ($this->newStatus == 'Enviado') {
            $mail->line('¡Buenas noticias! Tu pedido ha sido enviado.');
            if ($this->order->shipment && $this->order->shipment->tracking_number) {
                $mail->line('Número de seguimiento: ' . $this->order->shipment->tracking_number);
            }
        } elseif ($this->newStatus == 'Entregado') {
            $mail->line('¡Tu pedido ha sido entregado con éxito!');
            $mail->line('Esperamos que disfrutes de tu compra.');
        }
        
        $mail->action('Ver Detalles del Pedido', $url)
             ->line('Gracias por comprar con nosotros.');
        
        return $mail;
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
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'message' => 'Tu pedido #' . $this->order->order_number . ' ha cambiado a: ' . $this->newStatus
        ];
    }
}