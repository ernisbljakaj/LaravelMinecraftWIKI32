<?php

namespace App\Filament\Resources\Servers\Schemas;

use Filament\Forms\Components\MultiSelect;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ServerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('ip')
                    ->label('Server-Adresse')
                    ->required()
                    ->placeholder('play.beispiel.ch')
                    ->maxLength(255),

                TextInput::make('version')
                    ->label('Minecraft-Version')
                    ->default('1.21')
                    ->maxLength(50),

                Select::make('mode')
                    ->label('Spielmodus')
                    ->options([
                        'Survival' => 'Survival',
                        'Creative' => 'Creative',
                        'PvP' => 'PvP',
                        'Skyblock' => 'Skyblock',
                        'Bedwars' => 'Bedwars',
                        'Minigames' => 'Minigames',
                        'Anarchy' => 'Anarchy',
                    ])
                    ->default('Survival')
                    ->required(),

                Textarea::make('description')
                    ->label('Beschreibung')
                    ->rows(4)
                    ->columnSpanFull(),

                MultiSelect::make('tags')
                    ->label('Tags')
                    ->relationship('tags', 'name')
                    ->columnSpanFull(),

                Toggle::make('approved')
                    ->label('Freigegeben')
                    ->helperText('Server wurde von einem Admin geprüft.')
                    ->default(false),

                Toggle::make('featured')
                    ->label('Featured')
                    ->default(false),
            ])
            ->columns(2);
    }
}
