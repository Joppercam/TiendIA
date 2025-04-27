<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class PerformanceService
{
    /**
     * Optimiza una imagen para la web.
     *
     * @param string $path Ruta de la imagen original
     * @param array $sizes Tamaños a generar ['thumb' => [200, 200], 'medium' => [600, 600]]
     * @param int $quality Calidad de compresión (1-100)
     * @param bool $webp Generar versión WebP
     * @return array Rutas a las imágenes optimizadas
     */
    public function optimizeImage(string $path, array $sizes = [], int $quality = 80, bool $webp = true)
    {
        $disk = Storage::disk('public');
        
        if (!$disk->exists($path)) {
            throw new \InvalidArgumentException("La imagen {$path} no existe.");
        }

        $originalImage = Image::make($disk->path($path));
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $filename = pathinfo($path, PATHINFO_FILENAME);
        $directory = pathinfo($path, PATHINFO_DIRNAME);
        
        $results = ['original' => $path];

        // Optimizamos la imagen original
        $originalImage->save($disk->path($path), $quality);
        
        // Generamos WebP de la original si está habilitado
        if ($webp) {
            $webpPath = "{$directory}/{$filename}.webp";
            $originalImage->encode('webp', $quality)->save($disk->path($webpPath));
            $results['original_webp'] = $webpPath;
        }

        // Generamos los tamaños adicionales
        foreach ($sizes as $sizeName => $dimensions) {
            $width = $dimensions[0] ?? null;
            $height = $dimensions[1] ?? null;
            
            $resizedImage = clone $originalImage;
            
            // Redimensionamos manteniendo proporciones
            if ($width && $height) {
                $resizedImage->fit($width, $height);
            } elseif ($width) {
                $resizedImage->widen($width, function ($constraint) {
                    $constraint->upsize();
                });
            } elseif ($height) {
                $resizedImage->heighten($height, function ($constraint) {
                    $constraint->upsize();
                });
            }

            // Guardamos versión redimensionada
            $resizedPath = "{$directory}/{$filename}_{$sizeName}.{$extension}";
            $resizedImage->save($disk->path($resizedPath), $quality);
            $results[$sizeName] = $resizedPath;
            
            // Generamos WebP si está habilitado
            if ($webp) {
                $webpResizedPath = "{$directory}/{$filename}_{$sizeName}.webp";
                $resizedImage->encode('webp', $quality)->save($disk->path($webpResizedPath));
                $results["{$sizeName}_webp"] = $webpResizedPath;
            }
        }

        return $results;
    }

    /**
     * Optimiza y combina archivos CSS.
     *
     * @param array $files Rutas de los archivos CSS
     * @param string $outputPath Ruta donde guardar el archivo combinado
     * @return string Ruta al archivo optimizado
     */
    public function optimizeCss(array $files, string $outputPath)
    {
        $combinedCss = '';
        
        foreach ($files as $file) {
            if (!file_exists($file)) {
                continue;
            }
            
            $css = file_get_contents($file);
            
            // Eliminamos comentarios
            $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
            
            // Eliminamos espacios en blanco innecesarios
            $css = preg_replace('/\s+/', ' ', $css);
            $css = str_replace(': ', ':', $css);
            $css = str_replace('{ ', '{', $css);
            $css = str_replace(' }', '}', $css);
            $css = str_replace('; ', ';', $css);
            
            $combinedCss .= $css;
        }
        
        // Guardamos el archivo combinado
        $disk = Storage::disk('public');
        $disk->put($outputPath, $combinedCss);
        
        return $outputPath;
    }
    
    /**
     * Optimiza y combina archivos JavaScript.
     *
     * @param array $files Rutas de los archivos JS
     * @param string $outputPath Ruta donde guardar el archivo combinado
     * @return string Ruta al archivo optimizado
     */
    public function optimizeJs(array $files, string $outputPath)
    {
        $combinedJs = '';
        
        foreach ($files as $file) {
            if (!file_exists($file)) {
                continue;
            }
            
            $js = file_get_contents($file);
            
            // Agregamos punto y coma al final por seguridad en la combinación
            if (substr(trim($js), -1) !== ';') {
                $js .= ';';
            }
            
            $combinedJs .= $js;
        }
        
        // Guardamos el archivo combinado
        $disk = Storage::disk('public');
        $disk->put($outputPath, $combinedJs);
        
        return $outputPath;
    }
    
    /**
     * Genera y gestiona un hash para los recursos estáticos (CSS/JS).
     * Útil para invalidar caché del navegador.
     *
     * @param string $path Ruta al archivo
     * @return string Hash para versionar
     */
    public function getAssetHash(string $path)
    {
        $cacheKey = 'asset_hash_' . md5($path);
        
        return Cache::rememberForever($cacheKey, function () use ($path) {
            $disk = Storage::disk('public');
            
            if (!$disk->exists($path)) {
                return md5(time());
            }
            
            return md5($disk->lastModified($path));
        });
    }
    
    /**
     * Limpia la caché de assets.
     *
     * @param string|null $path Ruta específica o null para limpiar toda la caché
     */
    public function clearAssetCache(?string $path = null)
    {
        if ($path) {
            Cache::forget('asset_hash_' . md5($path));
        } else {
            // Eliminamos todas las claves que comiencen con asset_hash_
            $keys = Cache::get('asset_hash_keys', []);
            foreach ($keys as $key) {
                Cache::forget($key);
            }
            Cache::forget('asset_hash_keys');
        }
    }
}