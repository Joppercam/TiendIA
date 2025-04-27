<?php

namespace App\Http\Middleware;

use App\Services\SeoService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class SeoMiddleware
{
    protected $seoService;
    
    public function __construct(SeoService $seoService)
    {
        $this->seoService = $seoService;
    }
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Procesamos redirecciones primero
        $currentPath = $request->path();
        $redirect = $this->seoService->processRedirect('/' . $currentPath);
        
        if ($redirect) {
            [$targetUrl, $statusCode] = $redirect;
            return redirect($targetUrl, $statusCode);
        }
        
        // Continuamos con la solicitud
        $response = $next($request);
        
        // Si la respuesta no es HTML, no procesamos los metadatos
        if (!$this->isHtmlResponse($response)) {
            return $response;
        }
        
        // Procesamos la inserción de metadatos SEO si están disponibles
        $this->processMetaTags($request, $response);
        
        return $response;
    }
    
    /**
     * Verifica si la respuesta es de tipo HTML.
     *
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @return bool
     */
    protected function isHtmlResponse(Response $response): bool
    {
        $contentType = $response->headers->get('Content-Type');
        
        if (!$contentType) {
            return true; // Asumimos HTML si no hay Content-Type
        }
        
        return strpos($contentType, 'text/html') !== false;
    }
    
    /**
     * Procesa metadatos SEO y los inserta en la respuesta.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @return void
     */
    protected function processMetaTags(Request $request, Response $response): void
    {
        // Obtenemos el contenido de la respuesta
        $content = $response->getContent();
        
        // Si no hay modelo con metadatos SEO, no continuamos
        if (!isset($request->seoModel)) {
            return;
        }
        
        $model = $request->seoModel;
        
        // Si el modelo implementa HasSeo
        if (method_exists($model, 'getSeoMetaTags')) {
            $metaTags = $model->getSeoMetaTags();
            $structuredData = $model->getStructuredData();
            
            // Reemplazamos title
            if (isset($metaTags['title'])) {
                $content = preg_replace('/<title>.*?<\/title>/i', "<title>{$metaTags['title']}</title>", $content);
            }
            
            // Construimos meta tags
            $metaTagsHtml = '';
            
            foreach ($metaTags as $name => $content) {
                if ($name === 'title') continue; // Ya procesado

                if ($name === 'description' || $name === 'keywords') {
                    $metaTagsHtml .= "<meta name=\"{$name}\" content=\"{$content}\">\n";
                } elseif (strpos($name, 'og:') === 0) {
                    $metaTagsHtml .= "<meta property=\"{$name}\" content=\"{$content}\">\n";
                } elseif (strpos($name, 'twitter:') === 0) {
                    $metaTagsHtml .= "<meta name=\"{$name}\" content=\"{$content}\">\n";
                } elseif ($name === 'canonical') {
                    $metaTagsHtml .= "<link rel=\"canonical\" href=\"{$content}\">\n";
                } elseif ($name === 'robots') {
                    $metaTagsHtml .= "<meta name=\"robots\" content=\"{$content}\">\n";
                }
            }
            
            // Agregamos datos estructurados si existen
            if ($structuredData) {
                $jsonLD = json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
                $metaTagsHtml .= "<script type=\"application/ld+json\">\n{$jsonLD}\n</script>\n";
            }
            
            // Insertamos meta tags antes del cierre de </head>
            $content = preg_replace('/<\/head>/i', $metaTagsHtml . '</head>', $content);
            
            // Actualizamos el contenido de la respuesta
            $response->setContent($content);
        }
    }
}