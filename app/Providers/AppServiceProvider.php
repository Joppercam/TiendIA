<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\CartService;
use App\Services\CheckoutService;
use App\Services\PaymentService;
use App\Services\InventoryService;
use App\Services\PromotionService;
use App\Services\MarketingService;
use App\Services\InvoiceService;
use App\Services\PaymentGatewayService;
use App\Services\PaymentProcessorService;
use App\Services\SeoService;
use App\Services\PerformanceService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Registrar el servicio de carrito
        $this->app->singleton(CartService::class, function ($app) {
            return new CartService();
        });

        // Registrar el servicio de inventario
        $this->app->singleton(InventoryService::class, function ($app) {
            return new InventoryService();
        });

        // Registrar el servicio de pagos
        $this->app->singleton(PaymentService::class, function ($app) {
            return new PaymentService();
        });

        // Registrar el servicio de checkout con sus dependencias
        $this->app->singleton(CheckoutService::class, function ($app) {
            return new CheckoutService(
                $app->make(CartService::class),
                $app->make(PaymentService::class),
                $app->make(InventoryService::class)
            );
        });

        // Registrar servicios de marketing y promociones
        $this->app->singleton(PromotionService::class, function ($app) {
            return new PromotionService();
        });
        
        $this->app->singleton(MarketingService::class, function ($app) {
            return new MarketingService();
        });

        // Registrar el servicio de pasarelas de pago
        $this->app->singleton(PaymentGatewayService::class, function ($app) {
            return new PaymentGatewayService();
        });
        
        // Registrar el servicio de facturas
        $this->app->singleton(InvoiceService::class, function ($app) {
            return new InvoiceService();
        });

        // Registramos los servicios SEO
        $this->app->singleton(SeoService::class, function ($app) {
            return new SeoService();
        });

        $this->app->singleton(PerformanceService::class, function ($app) {
            return new PerformanceService();
        });

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Configuración para evitar errores con índices demasiado largos en MySQL
        Schema::defaultStringLength(191);
        
        // Compartimos datos SEO con todas las vistas
        View::composer('*', function ($view) {
            // Compartimos la configuración global de SEO
            $view->with('seoConfig', config('seo'));
        });
    }

    
}
