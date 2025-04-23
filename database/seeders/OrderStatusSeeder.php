<?php

namespace Database\Seeders;

use App\Models\OrderStatus;
use Illuminate\Database\Seeder;

class OrderStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $statuses = [
            [
                'name' => 'Pendiente',
                'slug' => 'pending',
                'color' => '#ffc107', // Amarillo
                'description' => 'El pedido ha sido recibido pero no ha sido procesado todavía.',
                'order' => 1,
                'is_default' => true,
            ],
            [
                'name' => 'Pago Pendiente',
                'slug' => 'payment_pending',
                'color' => '#17a2b8', // Celeste
                'description' => 'El pedido está a la espera de confirmación de pago.',
                'order' => 2,
                'is_default' => false,
            ],
            [
                'name' => 'Procesando',
                'slug' => 'processing',
                'color' => '#007bff', // Azul
                'description' => 'El pedido ha sido confirmado y está siendo preparado.',
                'order' => 3,
                'is_default' => false,
            ],
            [
                'name' => 'Enviado',
                'slug' => 'shipped',
                'color' => '#6f42c1', // Morado
                'description' => 'El pedido ha sido enviado al cliente.',
                'order' => 4,
                'is_default' => false,
            ],
            [
                'name' => 'Entregado',
                'slug' => 'delivered',
                'color' => '#28a745', // Verde
                'description' => 'El pedido ha sido entregado con éxito al cliente.',
                'order' => 5,
                'is_default' => false,
            ],
            [
                'name' => 'Cancelado',
                'slug' => 'cancelled',
                'color' => '#dc3545', // Rojo
                'description' => 'El pedido ha sido cancelado.',
                'order' => 6,
                'is_default' => false,
            ],
            [
                'name' => 'Reembolsado',
                'slug' => 'refunded',
                'color' => '#fd7e14', // Naranja
                'description' => 'El pedido ha sido reembolsado total o parcialmente.',
                'order' => 7,
                'is_default' => false,
            ],
        ];

        foreach ($statuses as $status) {
            OrderStatus::updateOrCreate(
                ['slug' => $status['slug']],
                $status
            );
        }
    }
}