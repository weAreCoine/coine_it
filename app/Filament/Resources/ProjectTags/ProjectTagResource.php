<?php

namespace App\Filament\Resources\ProjectTags;

use App\Filament\Resources\ProjectTags\Pages\CreateProjectTag;
use App\Filament\Resources\ProjectTags\Pages\EditProjectTag;
use App\Filament\Resources\ProjectTags\Pages\ListProjectTags;
use App\Filament\Resources\ProjectTags\Schemas\ProjectTagForm;
use App\Filament\Resources\ProjectTags\Tables\ProjectTagsTable;
use App\Models\ProjectTag;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProjectTagResource extends Resource
{
    protected static ?string $model = ProjectTag::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?string $recordTitleAttribute = 'Tag Progetto';

    public static function getNavigationLabel(): string
    {
        return __('Tags');
    }

    public static function getNavigationGroup(): string
    {
        return __('Projects');
    }

    public static function form(Schema $schema): Schema
    {
        return ProjectTagForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProjectTagsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProjectTags::route('/'),
            'create' => CreateProjectTag::route('/create'),
            'edit' => EditProjectTag::route('/{record}/edit'),
        ];
    }
}
