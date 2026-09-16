<?php

namespace App\Filament\Resources\WikiPages\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class WikiPageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Titel')
                    ->required()
                    ->maxLength(255)
                    ->live()
                    ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),

                TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->maxLength(255),

                Select::make('category')
                    ->label('Kategorie')
                    ->options([
                        'Redstone' => 'Redstone',
                        'Farmen' => 'Farmen',
                        'Bauen' => 'Bauen',
                        'Enchanting' => 'Enchanting',
                        'Biome' => 'Biome',
                        'Mobs' => 'Mobs',
                        'General' => 'General',
                    ])
                    ->default('General')
                    ->required(),

                RichEditor::make('content')
                    ->label('Inhalt')
                    ->required()
                    ->columnSpanFull(),

                TextInput::make('excerpt')
                    ->label('Kurzbeschreibung')
                    ->maxLength(300)
                    ->columnSpanFull(),

                TextInput::make('image')
                    ->label('Bild-URL')
                    ->placeholder('https://…')
                    ->columnSpanFull(),

                Toggle::make('approved')
                    ->label('Freigegeben')
                    ->default(false),
            ])
            ->columns(2);
    }
}
