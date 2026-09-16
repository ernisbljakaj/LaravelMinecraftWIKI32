<?php

namespace App\Filament\Resources\Comments\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CommentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('body')
                    ->label('Kommentar')
                    ->required()
                    ->rows(4)
                    ->columnSpanFull(),

                Toggle::make('approved')
                    ->label('Freigegeben')
                    ->default(true),
            ]);
    }
}
