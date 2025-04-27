<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuración SEO
    |--------------------------------------------------------------------------
    |
    | Este archivo contiene la configuración para el módulo de SEO y Optimización.
    |
    */

    // Configuración general
    'site_name' => env('APP_NAME', 'TiendIA'),
    'separator' => ' - ',
    'default_title' => 'Tienda Online',
    'default_description' => 'Tienda de e-commerce con amplio catálogo de productos y servicios.',
    'default_keywords' => 'tienda, ecommerce, productos, servicios, compras online',
    
    // Configuración de redes sociales
    'social' => [
        'twitter' => [
            'site' => '@tiendIA',
            'card' => 'summary_large_image',
        ],
        'facebook' => [
            'app_id' => env('FACEBOOK_APP_ID', ''),
        ],
    ],
    
    // Configuración de sitemap
    'sitemap' => [
        'default_priority' => 0.5,
        'default_frequency' => 'weekly',
        'default_path' => 'storage/sitemaps',
    ],
    
    // Configuración de caché
    'cache' => [
        'enabled' => true,
        'ttl' => 3600, // 1 hora
    ],
    
    // Configuración de optimización de imágenes
    'images' => [
        'default_quality' => 80,
        'enable_webp' => true,
        'sizes' => [
            'thumbnail' => [150, 150],
            'small' => [300, 300],
            'medium' => [600, 600],
            'large' => [1200, 1200],
        ],
    ],
    
    // Configuración de seguimiento (analytics)
    'analytics' => [
        'google' => [
            'enabled' => env('GOOGLE_ANALYTICS_ENABLED', false),
            'id' => env('GOOGLE_ANALYTICS_ID', ''),
        ],
        'facebook_pixel' => [
            'enabled' => env('FACEBOOK_PIXEL_ENABLED', false),
            'id' => env('FACEBOOK_PIXEL_ID', ''),
        ],
    ],
    
    // Configuración de bots
    'robots' => [
        'index' => env('ROBOTS_INDEX', true),
        'follow' => env('ROBOTS_FOLLOW', true),
        'custom' => env('ROBOTS_CUSTOM', null),
    ],
    
    // URLs canónicas
    'canonical' => [
        'enabled' => true,
        'force_https' => true,
    ],
    
    // Tipos de datos estructurados disponibles
    'structured_data_types' => [
        'product' => \App\Models\Product::class,
        'local_business' => 'App\StructuredData\LocalBusiness',
        'bread_crumb' => 'App\StructuredData\BreadCrumb',
        'article' => 'App\StructuredData\Article',
    ],
    
    // Configuración de rendimiento
    'performance' => [
        'minify_html' => env('PERFORMANCE_MINIFY_HTML', false),
        'minify_css' => env('PERFORMANCE_MINIFY_CSS', true),
        'minify_js' => env('PERFORMANCE_MINIFY_JS', true),
        'combine_css' => env('PERFORMANCE_COMBINE_CSS', true),
        'combine_js' => env('PERFORMANCE_COMBINE_JS', true),
        'defer_js' => env('PERFORMANCE_DEFER_JS', true),
    ],
];