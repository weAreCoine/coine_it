<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SeoMetadata extends Component
{
    public string $title;

    public string $description;

    public string $canonicalUrl;

    public string $robots;

    public string $ogType;

    public string $ogSiteName;

    public string $ogImage;

    public string $twitterCard;

    public ?array $article;

    public ?array $creativeWork;

    public array $breadcrumbs;

    public function __construct(
        ?string $title = null,
        ?string $description = null,
        ?string $canonicalUrl = null,
        string $robots = 'index, follow',
        string $ogType = 'website',
        ?string $ogImage = null,
        string $twitterCard = 'summary_large_image',
        ?array $article = null,
        ?array $creativeWork = null,
        array $breadcrumbs = [],
    ) {
        $this->title = $title ?? config('app.name');
        $this->description = $description ?? __('Siamo un team di esperti multidisciplinari che offrono servizi completi per lo sviluppo di siti web personalizzati e app mobile, consulenza marketing strategica e operativa, SEO, content marketing e molto altro.');
        $this->canonicalUrl = $canonicalUrl ?? url()->current();
        $this->robots = $robots;
        $this->ogType = $article ? 'article' : $ogType;
        $this->ogSiteName = config('app.name');
        $this->ogImage = $ogImage ?? asset('images/home/better_call_coine.webp');
        $this->twitterCard = $twitterCard;
        $this->article = $article;
        $this->creativeWork = $creativeWork;
        $this->breadcrumbs = $breadcrumbs;
    }

    public function organizationSchema(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'Coiné',
            'url' => config('app.url'),
            'logo' => asset('images/logo.png'),
            'description' => __('Con oltre 10 anni di esperienza, Coiné è il tuo partner ideale per crescere in modo sostenibile affrontando le sfide del mondo digitale.'),
            'address' => [
                '@type' => 'PostalAddress',
                'addressCountry' => 'IT',
            ],
            'sameAs' => [
                // Add social profiles here if available
            ],
        ];
    }

    public function articleSchema(): ?array
    {
        if (! $this->article) {
            return null;
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $this->title,
            'description' => $this->description,
            'image' => $this->ogImage,
            'author' => [
                '@type' => 'Person',
                'name' => $this->article['author'] ?? 'Coiné',
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'Coiné',
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => asset('images/logo.png'),
                ],
            ],
            'datePublished' => $this->article['published_time'] ?? null,
            'dateModified' => $this->article['modified_time'] ?? $this->article['published_time'] ?? null,
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $this->canonicalUrl,
            ],
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    public function creativeWorkSchema(): ?array
    {
        if (! $this->creativeWork) {
            return null;
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'CreativeWork',
            'headline' => $this->title,
            'description' => $this->description,
            'image' => $this->ogImage,
            'author' => [
                '@type' => 'Person',
                'name' => $this->creativeWork['author'] ?? 'Coiné',
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'Coiné',
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => asset('images/logo.png'),
                ],
            ],
            'datePublished' => $this->creativeWork['published_time'] ?? null,
            'dateModified' => $this->creativeWork['modified_time'] ?? $this->creativeWork['published_time'] ?? null,
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $this->canonicalUrl,
            ],
        ];
    }

    public function breadcrumbSchema(): ?array
    {
        if (empty($this->breadcrumbs)) {
            return null;
        }

        $items = [];
        foreach ($this->breadcrumbs as $index => $breadcrumb) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $breadcrumb['name'],
                'item' => $breadcrumb['url'] ?? null,
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items,
        ];
    }

    public function render(): View
    {
        return view('components.seo-metadata');
    }
}
