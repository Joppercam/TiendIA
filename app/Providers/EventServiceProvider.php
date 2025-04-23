<?php
// app/Providers/EventServiceProvider.php

namespace App\Providers;

use App\Events\OrderCancelled;
use App\Events\OrderPlaced;
use App\Events\OrderStatusChanged;
use App\Listeners\AdjustInventoryAfterCancel;
use App\Listeners\NotifyAdminAboutNewOrder;
use App\Listeners\NotifyCustomerAboutCancel;
use App\Listeners\NotifyCustomerAboutOrderStatus;
use App\Listeners\ProcessOrderPlaced;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        // Otros eventos existentes...
        
        OrderPlaced::class => [
            ProcessOrderPlaced::class,
            NotifyAdminAboutNewOrder::class,
        ],
        
        OrderStatusChanged::class => [
            NotifyCustomerAboutOrderStatus::class,
        ],
        
        OrderCancelled::class => [
            AdjustInventoryAfterCancel::class,
            NotifyCustomerAboutCancel::class,
        ],
    ];

    // Resto del archivo...
}