<?php

declare(strict_types=1);

namespace App\Entities;

use App\Enums\Services;

class Service
{
    public function __construct(
        public string $title,
        public string $shortDescription,
        public string $backgroundClass,
        public ?string $description = null,
        public ?string $pageUrl = null,
        public bool $textBlack = true,
    ) {}

    public static function getServiceLabel(Services $service): string
    {
        return match ($service) {
            Services::Teaching => __('Formazione'),
            Services::WebDesign => __('Web Design'),
            Services::DigitalMarketing => __('Online Advertising'),
            Services::Localization => __('Cultural Localization'),
            Services::WebDeveloping => __('Sviluppo Web'),
            Services::AppDeveloping => __('Sviluppo App Mobile'),
            Services::Content => __('Content Marketing'),
            Services::SocialMedia => __('Strategia Social Media'),
            Services::MarketingConsulting => __('Marketing Consulting'),
            Services::SEO => __('SEO'),
            Services::Notion => __('Notion'),
            default => __('Consulenza'),
        };
    }

    public static function getServiceColor(Services $service): string
    {
        return match ($service) {
            Services::Teaching => 'green-500',
            Services::WebDesign => 'indigo-500',
            Services::DigitalMarketing => 'blue-500',
            Services::Localization => 'pink-500',
            Services::WebDeveloping => 'amber-500',
            Services::AppDeveloping => 'orange-500',
            Services::Content => 'red-500',
            Services::SocialMedia => 'green-600',
            Services::MarketingConsulting => 'slate-600',
            Services::SEO => 'pink-700',
            Services::Notion => 'black',
            default => __('Consulenza'),
        };
    }
}
