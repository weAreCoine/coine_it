<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class(['dark' => ($appearance ?? 'system') == 'dark', 'overflow-x-clip'])>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title inertia>{{ config('app.name', 'Laravel') }}</title>

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    @stack('seo')
    <x-seo-metadata
        :title="View::yieldContent('page.title', config('app.name'))"
        :description="$seoDescription ?? null"
        :og-image="$seoImage ?? null"
        :article="$seoArticle ?? null"
        :breadcrumbs="$seoBreadcrumbs ?? []"
    />
    <x-metapixel-head />
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter-tight:400,400i,500,500i,600,600i|press-start-2p:400"
          rel="stylesheet" />
    @viteReactRefresh
    @vite(['resources/js/app.tsx', "resources/js/pages/{$page['component']}.tsx"])
    @inertiaHead
</head>
<body class="font-sans antialiased md:subpixel-antialiased scroll-smooth overflow-x-clip">
<x-metapixel-body />
@inertia
</body>
</html>
