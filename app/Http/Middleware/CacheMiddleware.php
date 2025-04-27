<?php

namespace App\Http\Middleware;

use App\Services\PerformanceService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CacheMiddleware
{
    protected $performanceService;
    
    public function __construct(PerformanceService $performanceService)
    {
        $this->performanceService = $performanceService;
    }
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $seconds  Tiempo de caché en segundos
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $seconds = null): Response
    {
        // No aplicamos caché si es un usuario autenticado, método POST o está en modo debug
        if ($request->user() || $request->isMethod('POST') || config('app.debug')) {
            return $next($request);
        }
        
        // Generamos una clave única para esta solicitud
        $cacheKey = 'page_cache_' . md5($request->fullUrl());
        
        // Si hay caché disponible, la devolvemos
        if (Cache::has($cacheKey)) {
            return response(Cache::get($cacheKey));
        }
        
        // Obtenemos la respuesta
        $response = $next($request);
        
        // Solo cachemos respuestas HTML exitosas
        if ($this->shouldCache($response)) {
            $seconds = $seconds ?: config('cache.page_cache_ttl', 3600); // 1 hora por defecto
            
            Cache::put($cacheKey, $response->getContent(), now()->addSeconds($seconds));
        }
        
        return $response;
    }
    
    /**
     * Determina si la respuesta debe ser cacheada.
     *
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @return bool
     */
    protected function shouldCache(Response $response): bool
    {
        // Verificamos el código de estado y tipo de contenido
        $contentType = $response->headers->get('Content-Type');
        
        return $response->isSuccessful() && 
               (strpos($contentType, 'text/html') !== false || !$contentType) &&
               $response->getStatusCode() == 200;
    }
}