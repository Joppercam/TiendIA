@php
    // Si no hay modelo SEO, usamos valores por defecto
    $title = $title ?? config('seo.default_title');
    $description = $description ?? config('seo.default_description');
    $keywords = $keywords ?? config('seo.default_keywords');
    
    // Si hay un modelo con trait HasSeo
    if (isset($model) && method_exists($model, 'getSeoMetaTags')) {
        $metaTags = $model->getSeoMetaTags();
        $title = $metaTags['title'] ?? $title;
        $description = $metaTags['description'] ?? $description;
        $keywords = $metaTags['keywords'] ?? $keywords;
        
        // Agregamos el nombre del sitio si está configurado
        if (config('seo.site_name') && !str_contains($title, config('seo.site_name'))) {
            $title .= config('seo.separator') . config('seo.site_name');
        }
    }
    
    // Datos estructurados (JSON-LD)
    $structuredData = $model->getStructuredData() ?? null if (isset($model) && method_exists($model, 'getStructuredData'));
@endphp

{{-- Title tag --}}
<title>{{ $title }}</title>

{{-- Meta tags básicos --}}
<meta name="description" content="{{ $description }}">
@if($keywords)
<meta name="keywords" content="{{ $keywords }}">
@endif

{{-- Meta tags específicos de SEO si hay un modelo --}}
@if(isset($model) && method_exists($model, 'getSeoMetaTags'))
    @php
        $metaTags = $model->getSeoMetaTags();
    @endphp
    
    {{-- Open Graph tags --}}
    @if(isset($metaTags['og:title']) || isset($metaTags['og:description']) || isset($metaTags['og:image']))
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:title" content="{{ $metaTags['og:title'] ?? $title }}">
        <meta property="og:description" content="{{ $metaTags['og:description'] ?? $description }}">
        @if(isset($metaTags['og:image']))
            <meta property="og:image" content="{{ $metaTags['og:image'] }}">
        @endif
        @if(config('seo.social.facebook.app_id'))
            <meta property="fb:app_id" content="{{ config('seo.social.facebook.app_id') }}">
        @endif
    @endif
    
    {{-- Twitter Card tags --}}
    @if(isset($metaTags['twitter:title']) || isset($metaTags['twitter:description']) || isset($metaTags['twitter:image']))
        <meta name="twitter:card" content="{{ config('seo.social.twitter.card', 'summary_large_image') }}">
        @if(config('seo.social.twitter.site'))
            <meta name="twitter:site" content="{{ config('seo.social.twitter.site') }}">
        @endif
        <meta name="twitter:title" content="{{ $metaTags['twitter:title'] ?? $title }}">
        <meta name="twitter:description" content="{{ $metaTags['twitter:description'] ?? $description }}">
        @if(isset($metaTags['twitter:image']))
            <meta name="twitter:image" content="{{ $metaTags['twitter:image'] }}">
        @endif
    @endif
    
    {{-- Canonical URL --}}
    @if(isset($metaTags['canonical']))
        <link rel="canonical" href="{{ $metaTags['canonical'] }}">
    @endif
    
    {{-- Robots directives --}}
    @if(isset($metaTags['robots']))
        <meta name="robots" content="{{ $metaTags['robots'] }}">
    @endif
@else
    {{-- Configuración global de robots --}}
    @if(!config('seo.robots.index') || !config('seo.robots.follow'))
        <meta name="robots" content="{{ config('seo.robots.index') ? '' : 'noindex' }}{{ !config('seo.robots.index') && !config('seo.robots.follow') ? ',' : '' }}{{ config('seo.robots.follow') ? '' : 'nofollow' }}">
    @endif
    
    {{-- Canonical URL por defecto --}}
    @if(config('seo.canonical.enabled'))
        <link rel="canonical" href="{{ config('seo.canonical.force_https') ? secure_url(url()->current()) : url()->current() }}">
    @endif
@endif

{{-- Datos estructurados (JSON-LD) --}}
@if(isset($structuredData) && $structuredData)
    <script type="application/ld+json">
        {!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
@endif

{{-- Analytics --}}
@if(config('seo.analytics.google.enabled') && config('seo.analytics.google.id'))
    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('seo.analytics.google.id') }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ config('seo.analytics.google.id') }}');
    </script>
@endif

@if(config('seo.analytics.facebook_pixel.enabled') && config('seo.analytics.facebook_pixel.id'))
    <!-- Facebook Pixel Code -->
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '{{ config('seo.analytics.facebook_pixel.id') }}');
        fbq('track', 'PageView');
    </script>
    <noscript>
        <img height="1" width="1" style="display:none" 
            src="https://www.facebook.com/tr?id={{ config('seo.analytics.facebook_pixel.id') }}&ev=PageView&noscript=1"/>
    </noscript>
@endif