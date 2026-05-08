<?php

declare(strict_types=1);

namespace App\Filament\Resources\CookieConsents\Pages;

use App\Filament\Resources\CookieConsents\CookieConsentResource;
use App\Filament\Widgets\CookieConsentStatsWidget;
use Filament\Resources\Pages\ListRecords;

class ListCookieConsents extends ListRecords
{
    protected static string $resource = CookieConsentResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            CookieConsentStatsWidget::class,
        ];
    }
}
