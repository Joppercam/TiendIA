<?php

namespace App\Traits;

use App\Models\SeoMetadata;

trait HasSeo
{
    /**
     * Boot the trait.
     */
    public static function bootHasSeo()
    {
        static::deleting(function ($model) {
            // No eliminamos los metadatos automáticamente si es softDelete
            if (!method_exists($model, 'isForceDeleting') || $model->isForceDeleting()) {
                $model->seoMetadata()->delete();
            }
        });
    }

    /**
     * Get the model's SEO metadata.
     */
    public function seoMetadata()
    {
        return $this->morphOne(SeoMetadata::class, 'seoable');
    }

    /**
     * Get SEO title with fallback to model title/name.
     */
    public function getSeoTitle()
    {
        // Primero intentamos obtener el título personalizado de SEO
        if ($this->seoMetadata && $this->seoMetadata->title) {
            return $this->seoMetadata->title;
        }

        // Fallback al título/nombre del modelo
        // Intentamos buscar en diferentes atributos comunes
        foreach (['title', 'name', 'heading', 'subject'] as $attribute) {
            if (isset($this->$attribute)) {
                return $this->$attribute;
            }
        }

        // Si no hay nada, devolvemos el nombre de la clase como último recurso
        return class_basename($this);
    }

    /**
     * Get SEO description with fallback.
     */
    public function getSeoDescription()
    {
        if ($this->seoMetadata && $this->seoMetadata->meta_description) {
            return $this->seoMetadata->meta_description;
        }

        // Intentamos buscar en diferentes atributos comunes
        foreach (['description', 'excerpt', 'summary', 'content'] as $attribute) {
            if (isset($this->$attribute)) {
                // Limitamos a 160 caracteres que es lo recomendado para meta description
                return \Str::limit(strip_tags($this->$attribute), 160);
            }
        }

        return null;
    }

    /**
     * Get the full meta tags array.
     */
    public function getSeoMetaTags()
    {
        $tags = [
            'title' => $this->getSeoTitle(),
            'description' => $this->getSeoDescription(),
        ];

        if ($this->seoMetadata) {
            $seo = $this->seoMetadata;

            if ($seo->meta_keywords) {
                $tags['keywords'] = $seo->meta_keywords;
            }

            // Open Graph tags
            if ($seo->og_title || $seo->og_description || $seo->og_image) {
                $tags['og:title'] = $seo->og_title ?: $tags['title'];
                $tags['og:description'] = $seo->og_description ?: $tags['description'];
                
                if ($seo->og_image) {
                    $tags['og:image'] = $seo->og_image;
                }
            }

            // Twitter Card tags
            if ($seo->twitter_title || $seo->twitter_description || $seo->twitter_image) {
                $tags['twitter:card'] = 'summary_large_image';
                $tags['twitter:title'] = $seo->twitter_title ?: $tags['title'];
                $tags['twitter:description'] = $seo->twitter_description ?: $tags['description'];
                
                if ($seo->twitter_image) {
                    $tags['twitter:image'] = $seo->twitter_image;
                }
            }

            // Canonical URL
            if ($seo->canonical_url) {
                $tags['canonical'] = $seo->canonical_url;
            }

            // Robots directives
            if ($seo->no_index || $seo->no_follow) {
                $robots = [];
                if ($seo->no_index) $robots[] = 'noindex';
                if ($seo->no_follow) $robots[] = 'nofollow';
                $tags['robots'] = implode(',', $robots);
            }
        }

        return $tags;
    }

    /**
     * Get structured data (JSON-LD).
     */
    public function getStructuredData()
    {
        if ($this->seoMetadata && $this->seoMetadata->structured_data) {
            return $this->seoMetadata->structured_data;
        }

        return null;
    }
}