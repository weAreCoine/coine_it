<?php

namespace App\Filament\Resources\ProjectTools;

use App\Filament\Resources\ProjectTools\Pages\CreateProjectTool;
use App\Filament\Resources\ProjectTools\Pages\EditProjectTool;
use App\Filament\Resources\ProjectTools\Pages\ListProjectTools;
use App\Filament\Resources\ProjectTools\Schemas\ProjectToolForm;
use App\Filament\Resources\ProjectTools\Tables\ProjectToolsTable;
use App\Models\ProjectTool;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProjectToolResource extends Resource
{
    protected static ?string $model = ProjectTool::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    protected static ?string $recordTitleAttribute = 'Tool Progetto';

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('Tools');
    }

    public static function getNavigationGroup(): string
    {
        return __('Projects');
    }

    public static function form(Schema $schema): Schema
    {
        return ProjectToolForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProjectToolsTable::configure($table);
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
            'index' => ListProjectTools::route('/'),
            'create' => CreateProjectTool::route('/create'),
            'edit' => EditProjectTool::route('/{record}/edit'),
        ];
    }
}
