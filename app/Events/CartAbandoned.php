<?php

namespace App\Events;

use App\Models\Cart;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CartAbandoned
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $cart;

    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
    }
}