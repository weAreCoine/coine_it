{{-- Basic SEO Meta Tags --}}
<meta name="description" content="{{ $description }}">
<meta name="robots" content="{{ $robots }}">
<link rel="canonical" href="{{ $canonicalUrl }}">

{{-- Open Graph Meta Tags --}}
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:url" content="{{ $canonicalUrl }}">
<meta property="og:type" content="{{ $ogType }}">
<meta property="og:site_name" content="{{ $ogSiteName }}">
<meta property="og:image" content="{{ $ogImage }}">
<meta property="og:locale" content="{{ str_replace('_', '-', app()->getLocale()) }}">

{{-- Twitter Card Meta Tags --}}
<meta name="twitter:card" content="{{ $twitterCard }}">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $ogImage }}">

{{-- Article-specific Meta Tags --}}
@if($article)
    @if(isset($article['published_time']))
        <meta property="article:published_time" content="{{ $article['published_time'] }}">
    @endif
    @if(isset($article['modified_time']))
        <meta property="article:modified_time" content="{{ $article['modified_time'] }}">
    @endif
    @if(isset($article['author']))
        <meta property="article:author" content="{{ $article['author'] }}">
    @endif
    @if(isset($article['section']))
        <meta property="article:section" content="{{ $article['section'] }}">
    @endif
    @if(isset($article['tags']))
        @foreach($article['tags'] as $tag)
            <meta property="article:tag" content="{{ $tag }}">
        @endforeach
    @endif
@endif

{{-- JSON-LD Structured Data --}}
<script type="application/ld+json">
    {!! json_encode($organizationSchema(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>

@if($articleSchema())
    <script type="application/ld+json">
        {!! json_encode($articleSchema(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
@endif

@if($creativeWorkSchema())
    <script type="application/ld+json">
        {!! json_encode($creativeWorkSchema(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
@endif

@if($breadcrumbSchema())
    <script type="application/ld+json">
        {!! json_encode($breadcrumbSchema(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
@endif
