@props([
    'seo' => null,
    'title' => null,
    'description' => null,
    'image' => null,
    'type' => 'website',
    'robots' => null,
    'breadcrumbs' => [],
    'schemas' => [],
    'canonicalUrl' => null,
])

@php
    $lang        = current_lang();
    $siteName    = setting('site_name_' . $lang) ?: setting('site_name_en') ?: config('app.name', 'Brown Box');
    $seoTitle    = $title ?: (isset($seo) ? ($seo->getTranslation('title', $lang, false) ?: $seo->getTranslation('title', 'en', false)) : null);
    $seoDesc     = $description ?: (isset($seo) ? ($seo->getTranslation('description', $lang, false) ?: $seo->getTranslation('description', 'en', false)) : null);
    $seoKeywords = isset($seo) ? ($seo->getTranslation('keywords', $lang, false) ?: $seo->getTranslation('keywords', 'en', false)) : null;
    $ogTitle     = isset($seo) ? ($seo->getTranslation('og_title', $lang, false) ?: $seoTitle) : $seoTitle;
    $ogDesc      = isset($seo) ? ($seo->getTranslation('og_description', $lang, false) ?: $seoDesc) : $seoDesc;
    $ogImage     = $image ?: (isset($seo) && $seo->og_image ? asset_url($seo->og_image) : asset_url(setting('site_logo')));

    // Faceted navigation: strip filter/sort query params from the canonical URL on
    // listing routes, keeping only `page` (and only when it's beyond page 1).
    $isListingRoute = request()->routeIs('web.products.index', 'web.categories.show');
    if ($canonicalUrl) {
        $canonical = $canonicalUrl;
    } elseif (isset($seo) && $seo->canonical_url) {
        $canonical = $seo->canonical_url;
    } elseif ($isListingRoute) {
        $page = request()->integer('page');
        $canonical = url()->current() . ($page > 1 ? '?page=' . $page : '');
    } else {
        $canonical = url()->current();
    }

    $isNoindexRoute = request()->routeIs('web.checkout.*', 'web.search.*') || (isset($errorStatusCode) && $errorStatusCode == 404);
    $defaultRobots = 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1';
    $robots = $robots
        ?? ($pageRobots ?? null)
        ?? ($isNoindexRoute ? 'noindex, nofollow' : (isset($seo) && $seo->robots ? $seo->robots : $defaultRobots));

    $pageTitle = $seoTitle ? $seoTitle . ' — ' . $siteName : null;

    $currentPath = request()->path();
    $hreflangEn  = url(preg_replace('#^(ar|en)(/|$)#', 'en$2', $currentPath));
    $hreflangAr  = url(preg_replace('#^(ar|en)(/|$)#', 'ar$2', $currentPath));

    $yieldTitle = trim($__env->yieldContent('title'));

    $seoDescClean = $seoDesc ? Str::limit(strip_tags($seoDesc), 155) : null;
    $ogDescClean  = $ogDesc ? Str::limit(strip_tags($ogDesc), 155) : $seoDescClean;
@endphp

<title>{{ $pageTitle ?? ($yieldTitle ?: $siteName) }}</title>

<meta name="theme-color" content="#ffffff">

@if($seoDescClean)
    <meta name="description" content="{{ $seoDescClean }}">
@endif
@if($seoKeywords)
    <meta name="keywords" content="{{ $seoKeywords }}">
@endif
<meta name="robots" content="{{ $robots }}">
<link rel="canonical" href="{{ $canonical }}">
<link rel="alternate" hreflang="ar" href="{{ $hreflangAr }}">
<link rel="alternate" hreflang="en" href="{{ $hreflangEn }}">
<link rel="alternate" hreflang="x-default" href="{{ $hreflangAr }}">

{{-- Open Graph --}}
<meta property="og:type" content="{{ $type ?? 'website' }}">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:locale" content="{{ $lang === 'ar' ? 'ar_EG' : 'en_US' }}">
<meta property="og:locale:alternate" content="{{ $lang === 'ar' ? 'en_US' : 'ar_EG' }}">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:title" content="{{ $ogTitle ?? ($pageTitle ?? $siteName) }}">
@if($ogDescClean)
    <meta property="og:description" content="{{ $ogDescClean }}">
@endif
@if($ogImage)
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
@endif

{{-- Twitter / X Card --}}
<meta name="twitter:card" content="{{ $ogImage ? 'summary_large_image' : 'summary' }}">
<meta name="twitter:title" content="{{ $ogTitle ?? ($pageTitle ?? $siteName) }}">
@if($ogDescClean)
    <meta name="twitter:description" content="{{ $ogDescClean }}">
@endif
@if($ogImage)
    <meta name="twitter:image" content="{{ $ogImage }}">
@endif

{{-- Per-page overrides / additions (pushed via @push('meta') on individual views) --}}
@stack('meta')

{{-- Schema JSON-LD --}}
@if(isset($seo) && !empty($seo->schema_json))
    <script type="application/ld+json">{!! json_encode($seo->schema_json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}</script>
@endif

@if(request()->routeIs('web.home'))
    @php
        $orgSchema = [
            '@context' => 'https://schema.org',
            '@graph' => array_map(fn ($item) => array_filter($item), [
                array_filter([
                    '@type' => 'WebSite',
                    '@id' => url('/') . '#website',
                    'url' => url('/'),
                    'name' => $siteName,
                    'potentialAction' => [
                        '@type' => 'SearchAction',
                        'target' => [
                            '@type' => 'EntryPoint',
                            'urlTemplate' => route('web.search.index', ['lang' => $lang]) . '?search={search_term_string}',
                        ],
                        'query-input' => 'required name=search_term_string',
                    ],
                ]),
                array_filter([
                    '@type' => 'Organization',
                    '@id' => url('/') . '#organization',
                    'url' => url('/'),
                    'name' => $siteName,
                    'logo' => setting('site_logo') ? asset_url(setting('site_logo')) : null,
                    'sameAs' => array_values(array_filter([
                        setting('social_facebook'),
                        setting('social_instagram'),
                        setting('social_x'),
                    ])),
                ]),
            ]),
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($orgSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endif

@if(!empty($breadcrumbs))
    @php
        $breadcrumbSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => array_values($breadcrumbs),
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endif

@foreach ($schemas as $schema)
    <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endforeach
