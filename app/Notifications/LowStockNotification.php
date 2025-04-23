<?php

namespace App\Notifications;

use App\Models\Inventory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $inventory;

    /**
     * Create a new notification instance.
     */
    public function __construct(Inventory $inventory)
    {
        $this->inventory = $inventory;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $product = $this->inventory->product;
        
        return (new MailMessage)
            ->subject('Alerta de Stock Bajo - ' . $product->name)
            ->line('Este es un aviso de que el producto "' . $product->name . '" (SKU: ' . $product->sku . ') tiene un nivel de stock bajo.')
            ->line('Cantidad actual: ' . $this->inventory->quantity)
            ->line('Stock mínimo: ' . $this->inventory->min_stock)
            ->action('Ver Inventario', route('admin.inventory.show', $this->inventory))
            ->line('Por favor, considere reabastecer este producto pronto.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $product = $this->inventory->product;
        
        return [
            'inventory_id' => $this->inventory->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_sku' => $product->sku,
            'quantity' => $this->inventory->quantity,
            'min_stock' => $this->inventory->min_stock,
            'message' => 'Stock bajo para el producto "' . $product->name . '"'
        ];
    }
}