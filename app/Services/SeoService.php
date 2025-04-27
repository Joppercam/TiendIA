<?php

namespace App\Services;

use App\Models\SeoMetadata;
use App\Models\Redirect;
use App\Models\Sitemap;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SeoService
{
    /**
     * Genera o actualiza los metadatos SEO para un modelo.
     *
     * @param mixed $model El modelo al que se asocian los metadatos
     * @param array $data Los datos de metadatos SEO
     * @return SeoMetadata
     */
    public function updateOrCreateMetadata($model, array $data)
    {
        if (!$model->seoMetadata) {
            $metadata = new SeoMetadata($data);
            $model->seoMetadata()->save($metadata);
        } else {
            $model->seoMetadata->update($data);
        }

        // Limpiamos caché relacionada
        $this->clearMetadataCache($model);

        return $model->seoMetadata;
    }

    /**
     * Crea o actualiza una redirección.
     *
     * @param string $sourceUrl URL de origen
     * @param string $targetUrl URL de destino
     * @param int $statusCode Código de estado (301 o 302)
     * @return Redirect
     */
    public function createOrUpdateRedirect(string $sourceUrl, string $targetUrl, int $statusCode = 301)
    {
        // Normalizamos las URLs
        $sourceUrl = $this->normalizeUrl($sourceUrl);
        $targetUrl = $this->normalizeUrl($targetUrl);

        // Evitamos redirecciones a sí mismas o bucles directos
        if ($sourceUrl === $targetUrl) {
            throw new \InvalidArgumentException('La URL de origen y destino no pueden ser iguales.');
        }

        // Verificamos si ya existe una redirección con la URL de origen
        $redirect = Redirect::where('source_url', $sourceUrl)->first();

        if ($redirect) {
            $redirect->update([
                'target_url' => $targetUrl,
                'status_code' => $statusCode,
                'is_active' => true,
            ]);
        } else {
            $redirect = Redirect::create([
                'source_url' => $sourceUrl,
                'target_url' => $targetUrl,
                'status_code' => $statusCode,
                'is_active' => true,
            ]);
        }

        // Limpiamos caché de redirecciones
        $this->clearRedirectCache();

        return $redirect;
    }

    /**
     * Procesa una URL para verificar si tiene redirección.
     *
     * @param string $url La URL a verificar
     * @return array|null Si hay redirección, devuelve [url, statusCode], sino null
     */
    public function processRedirect(string $url)
    {
        $url = $this->normalizeUrl($url);

        // Intentamos obtener de caché primero
        $cacheKey = 'redirect_' . md5($url);
        
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        // Consultamos en la base de datos
        $redirect = Redirect::where('source_url', $url)
            ->where('is_active', true)
            ->first();

        if (!$redirect) {
            // Guardamos en caché que no hay redirección
            Cache::put($cacheKey, null, now()->addHours(24));
            return null;
        }

        // Incrementamos el contador de hits
        $redirect->increment('hits');
        $redirect->update(['last_accessed_at' => now()]);

        // Guardamos en caché
        $result = [$redirect->target_url, $redirect->status_code];
        Cache::put($cacheKey, $result, now()->addHours(24));

        return $result;
    }

    /**
     * Genera sitemaps para diferentes tipos de contenido.
     *
     * @param string|null $type Tipo específico o null para todos
     * @return bool
     */
    public function generateSitemaps(?string $type = null)
    {
        $query = Sitemap::where('is_active', true);
        
        if ($type) {
            $query->where('type', $type);
        }

        $sitemaps = $query->get();
        $sitemapIndex = [];

        foreach ($sitemaps as $sitemap) {
            $items = $this->getSitemapItems($sitemap->type);
            
            if (empty($items)) {
                continue;
            }

            $xml = $this->generateSitemapXml($items, $sitemap->frequency, $sitemap->priority);
            $filename = 'sitemap_' . $sitemap->type . '.xml';
            $path = 'sitemaps/' . $filename;
            
            // Guardamos el XML
            Storage::disk('public')->put($path, $xml);
            
            // Actualizamos el registro
            $sitemap->update([
                'filepath' => $path,
                'last_generated_at' => now(),
            ]);

            $sitemapIndex[] = [
                'loc' => url(Storage::disk('public')->url($path)),
                'lastmod' => now()->toAtomString(),
            ];
        }

        // Generamos el índice de sitemaps
        if (!empty($sitemapIndex)) {
            $indexXml = $this->generateSitemapIndexXml($sitemapIndex);
            Storage::disk('public')->put('sitemap.xml', $indexXml);
        }

        return true;
    }

    /**
     * Obtiene elementos para incluir en el sitemap según el tipo.
     *
     * @param string $type
     * @return array
     */
    protected function getSitemapItems(string $type)
    {
        $items = [];

        switch ($type) {
            case 'products':
                $products = \App\Models\Product::where('status', 'published')->get();
                foreach ($products as $product) {
                    $items[] = [
                        'loc' => route('shop.products.show', $product->slug),
                        'lastmod' => $product->updated_at->toAtomString(),
                        'changefreq' => 'weekly',
                        'priority' => '0.8',
                    ];
                }
                break;

            case 'categories':
                $categories = \App\Models\Category::where('is_active', true)->get();
                foreach ($categories as $category) {
                    $items[] = [
                        'loc' => route('shop.categories.show', $category->slug),
                        'lastmod' => $category->updated_at->toAtomString(),
                        'changefreq' => 'weekly',
                        'priority' => '0.7',
                    ];
                }
                break;

            case 'brands':
                $brands = \App\Models\Brand::where('is_active', true)->get();
                foreach ($brands as $brand) {
                    $items[] = [
                        'loc' => route('shop.brands.show', $brand->slug),
                        'lastmod' => $brand->updated_at->toAtomString(),
                        'changefreq' => 'monthly',
                        'priority' => '0.6',
                    ];
                }
                break;

            case 'pages':
                // Suponiendo que tenemos un modelo Page para las páginas estáticas
                if (class_exists('\\App\\Models\\Page')) {
                    $pages = \App\Models\Page::where('is_published', true)->get();
                    foreach ($pages as $page) {
                        $items[] = [
                            'loc' => route('pages.show', $page->slug),
                            'lastmod' => $page->updated_at->toAtomString(),
                            'changefreq' => 'monthly',
                            'priority' => '0.5',
                        ];
                    }
                }
                break;
        }

        return $items;
    }

    /**
     * Genera el XML para un sitemap.
     *
     * @param array $items
     * @param string $defaultFrequency
     * @param string $defaultPriority
     * @return string
     */
    protected function generateSitemapXml(array $items, string $defaultFrequency = 'weekly', string $defaultPriority = '0.5')
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($items as $item) {
            $xml .= '<url>';
            $xml .= '<loc>' . htmlspecialchars($item['loc']) . '</loc>';
            
            if (isset($item['lastmod'])) {
                $xml .= '<lastmod>' . $item['lastmod'] . '</lastmod>';
            }
            
            $xml .= '<changefreq>' . ($item['changefreq'] ?? $defaultFrequency) . '</changefreq>';
            $xml .= '<priority>' . ($item['priority'] ?? $defaultPriority) . '</priority>';
            $xml .= '</url>';
        }

        $xml .= '</urlset>';

        return $xml;
    }

    /**
     * Genera el XML para el índice de sitemaps.
     *
     * @param array $sitemaps
     * @return string
     */
    protected function generateSitemapIndexXml(array $sitemaps)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($sitemaps as $sitemap) {
            $xml .= '<sitemap>';
            $xml .= '<loc>' . htmlspecialchars($sitemap['loc']) . '</loc>';
            
            if (isset($sitemap['lastmod'])) {
                $xml .= '<lastmod>' . $sitemap['lastmod'] . '</lastmod>';
            }
            
            $xml .= '</sitemap>';
        }

        $xml .= '</sitemapindex>';

        return $xml;
    }

    /**
     * Normaliza una URL para uso en redirecciones.
     *
     * @param string $url
     * @return string
     */
    protected function normalizeUrl(string $url)
    {
        // Eliminamos el dominio base si está presente
        $url = Str::startsWith($url, url('/')) ? Str::after($url, url('/')) : $url;
        
        // Aseguramos que comience con /
        $url = '/' . ltrim($url, '/');
        
        // Eliminamos parámetros de consulta
        $url = strtok($url, '?');
        
        return $url;
    }

    /**
     * Limpia la caché relacionada con los metadatos de un modelo.
     *
     * @param mixed $model
     * @return void
     */
    protected function clearMetadataCache($model)
    {
        $modelType = get_class($model);
        $modelId = $model->getKey();
        
        // Limpiamos caché relacionada con este modelo
        Cache::forget("seo_metadata_{$modelType}_{$modelId}");
    }

    /**
     * Limpia la caché de redirecciones.
     *
     * @return void
     */
    protected function clearRedirectCache()
    {
        // Podríamos limpiar todas las cachés de redirecciones, pero eso sería ineficiente
        // En su lugar, podríamos tener un tag de caché para redirecciones, pero esto es simple por ahora
        Cache::forget('redirects_list');
    }
}