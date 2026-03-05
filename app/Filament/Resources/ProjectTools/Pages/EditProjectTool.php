<?php

namespace App\Filament\Resources\ProjectTools\Pages;

use App\Filament\Resources\ProjectTools\ProjectToolResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProjectTool extends EditRecord
{
    protected static string $resource = ProjectToolResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
