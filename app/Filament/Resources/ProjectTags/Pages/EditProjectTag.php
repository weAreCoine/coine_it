<?php

namespace App\Filament\Resources\ProjectTags\Pages;

use App\Filament\Resources\ProjectTags\ProjectTagResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProjectTag extends EditRecord
{
    protected static string $resource = ProjectTagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
