<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderCancellationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;
    protected $reason;

    /**
     * Create a new notification instance.
     *
     * @param Order $order
     * @param string|null $reason
     * @return void
     */
    public function __construct(Order $order, ?string $reason = null)
    {
        $this->order = $order;
        $this->reason = $reason;
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
            ->subject('Pedido #' . $this->order->order_number . ' Cancelado')
            ->greeting('Hola ' . ($notifiable->name ?? ''))
            ->line('Tu pedido #' . $this->order->order_number . ' ha sido cancelado.');
        
        if ($this->reason) {
            $mail->line('Motivo de la cancelación: ' . $this->reason);
        }
        
        // If payment was made, add information about refund
        if ($this->order->payment_status === 'completed' && $this->order->paid_at) {
            $mail->line('El reembolso será procesado en los próximos días hábiles.');
            $mail->line('Método de reembolso: Mismo método de pago original.');
        }
        
        $mail->action('Ver Detalles del Pedido', $url)
             ->line('Si tienes alguna pregunta, no dudes en contactarnos.');
        
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
            'reason' => $this->reason,
            'message' => 'Tu pedido #' . $this->order->order_number . ' ha sido cancelado.'
        ];
    }
}