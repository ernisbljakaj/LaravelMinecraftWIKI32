<?php

namespace App\Filament\Resources\WikiPages;

use App\Filament\Resources\WikiPages\Pages\CreateWikiPage;
use App\Filament\Resources\WikiPages\Pages\EditWikiPage;
use App\Filament\Resources\WikiPages\Pages\ListWikiPages;
use App\Filament\Resources\WikiPages\Schemas\WikiPageForm;
use App\Filament\Resources\WikiPages\Tables\WikiPagesTable;
use App\Models\WikiPage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class WikiPageResource extends Resource
{
    protected static ?string $model = WikiPage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static ?string $navigationLabel = 'Wiki-Artikel';

    protected static ?string $modelLabel = 'Wiki-Artikel';

    protected static ?string $pluralModelLabel = 'Wiki-Artikel';

    protected static string|UnitEnum|null $navigationGroup = 'Inhalte';

    public static function form(Schema $schema): Schema
    {
        return WikiPageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WikiPagesTable::configure($table);
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
            'index' => ListWikiPages::route('/'),
            'create' => CreateWikiPage::route('/create'),
            'edit' => EditWikiPage::route('/{record}/edit'),
        ];
    }
}
