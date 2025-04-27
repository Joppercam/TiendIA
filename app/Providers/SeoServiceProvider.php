<?php

namespace App\Providers;

use App\Services\SeoService;
use App\Services\PerformanceService;
use App\Http\Middleware\SeoMiddleware;
use App\Http\Middleware\CacheMiddleware;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Routing\Router;

class SeoServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Registramos el servicio de SEO como singleton
        $this->app->singleton(SeoService::class, function ($app) {
            return new SeoService();
        });

        // Registramos el servicio de Rendimiento como singleton
        $this->app->singleton(PerformanceService::class, function ($app) {
            return new PerformanceService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Registramos middleware
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('seo', SeoMiddleware::class);
        $router->aliasMiddleware('page-cache', CacheMiddleware::class);

        // Aplicamos middleware global si está habilitado
        if (config('seo.cache.enabled', true)) {
            $kernel = $this->app->make(Kernel::class);
            $kernel->pushMiddleware(SeoMiddleware::class);
        }

        // Publicamos configuración
        $this->publishes([
            __DIR__.'/../config/seo.php' => config_path('seo.php'),
        ], 'config');

        // Publicamos migraciones
        $this->publishes([
            __DIR__.'/../database/migrations/create_seo_tables.php.stub' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_seo_tables.php'),
        ], 'migrations');

        // Publicamos componentes
        $this->publishes([
            __DIR__.'/../resources/views/components/seo' => resource_path('views/components/seo'),
        ], 'views');

        // Cargamos vistas
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'seo');
    }
}