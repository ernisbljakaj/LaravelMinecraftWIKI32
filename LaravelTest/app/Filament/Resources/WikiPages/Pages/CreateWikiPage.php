<?php

namespace App\Filament\Resources\WikiPages\Pages;

use App\Filament\Resources\WikiPages\WikiPageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateWikiPage extends CreateRecord
{
    protected static string $resource = WikiPageResource::class;
}
