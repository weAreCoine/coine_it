<?php

namespace App\Enums;

use App\Entities\Service;
use Filament\Support\Contracts\HasLabel;

enum Services: string implements HasLabel
{
    case Teaching = 'Teaching';
    case WebDeveloping = 'WebDeveloping';
    case DigitalMarketing = 'DigitalMarketing';
    case AppDeveloping = 'AppDeveloping';
    case Localization = 'Localization';
    case WebDesign = 'WebDesign';
    case Content = 'Content';
    case SocialMedia = 'SocialMedia';
    case MarketingConsulting = 'MarketingConsulting';
    case SEO = 'SEO';
    case Notion = 'Notion';

    public function getLabel(): string
    {
        return Service::getServiceLabel($this);
    }
}
