<?php

namespace App\Filament\Resources\WikiPages\Pages;

use App\Filament\Resources\WikiPages\WikiPageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditWikiPage extends EditRecord
{
    protected static string $resource = WikiPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
