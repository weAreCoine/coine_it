<?php

declare(strict_types=1);

namespace App\Filament\Resources\CookieConsents;

use App\Filament\Resources\CookieConsents\Pages\ListCookieConsents;
use App\Filament\Resources\CookieConsents\Tables\CookieConsentsTable;
use App\Filament\Widgets\CookieConsentStatsWidget;
use App\Models\CookieConsent;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CookieConsentResource extends Resource
{
    protected static ?string $model = CookieConsent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static ?string $recordTitleAttribute = 'consent_id';

    public static function getNavigationGroup(): string
    {
        return __('Compliance');
    }

    public static function getNavigationLabel(): string
    {
        return __('Cookie Consents');
    }

    public static function getModelLabel(): string
    {
        return __('Cookie Consent');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Cookie Consents');
    }

    public static function table(Table $table): Table
    {
        return CookieConsentsTable::configure($table);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCookieConsents::route('/'),
        ];
    }

    /**
     * @return array<int, class-string>
     */
    public static function getWidgets(): array
    {
        return [
            CookieConsentStatsWidget::class,
        ];
    }
}
