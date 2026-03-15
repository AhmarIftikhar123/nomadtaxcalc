<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="{{ asset('assets/images/logos/favicon.png') }}" type="image/x-icon">
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-1678309253443916"
     crossorigin="anonymous"></script>

    @php
        $seo         = $page['props']['seo'] ?? [];
        $title       = $seo['title']       ?? config('app.name', 'NomadTaxCalc');
        $description = $seo['description'] ?? '';
        $canonical   = $seo['canonical']   ?? request()->url();
        $ogImage     = $seo['og_image']    ?? asset('images/og-default.jpg');
        $robots      = $seo['robots']      ?? 'index, follow';
        $schema      = $seo['schema']      ?? null;
    @endphp

    <title>{{ $title }}</title>
    <meta name="description"  content="{{ $description }}" />
    <meta name="robots"       content="{{ $robots }}" />
    <link rel="canonical"     href="{{ $canonical }}" />

    {{-- OpenGraph --}}
    <meta property="og:title"       content="{{ $title }}" />
    <meta property="og:description" content="{{ $description }}" />
    <meta property="og:image"       content="{{ $ogImage }}" />
    <meta property="og:url"         content="{{ $canonical }}" />
    <meta property="og:type"        content="{{ $seo['og_type'] ?? 'website' }}" />
    <meta property="og:site_name"   content="NomadTaxCalc" />

    {{-- Twitter Card --}}
    <meta name="twitter:card"        content="summary_large_image" />
    <meta name="twitter:title"       content="{{ $title }}" />
    <meta name="twitter:description" content="{{ $description }}" />
    <meta name="twitter:image"       content="{{ $ogImage }}" />

    {{-- Schema.org Structured Data (JSON-LD) --}}
    @if($schema)
        <script type="application/ld+json">{!! $schema !!}</script>
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @routes
    @viteReactRefresh
    @vite(['resources/js/app.jsx', "resources/js/Pages/{$page['component']}.jsx"])
    @inertiaHead
</head>

<body class="font-sans antialiased">
    @inertia
</body>

</html>