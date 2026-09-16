<?php

namespace App\Filament\Resources\WikiPages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class WikiPagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Titel')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                TextColumn::make('category')
                    ->label('Kategorie')
                    ->badge()
                    ->color('success')
                    ->searchable()
                    ->sortable(),
                BooleanColumn::make('approved')
                    ->label('Freigegeben'),
                TextColumn::make('user.name')
                    ->label('Autor')
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->label('Erstellt')
                    ->date('d.m.Y')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('approved')
                    ->label('Freigabe'),
                SelectFilter::make('category')
                    ->label('Kategorie')
                    ->options([
                        'Redstone' => 'Redstone',
                        'Farmen' => 'Farmen',
                        'Bauen' => 'Bauen',
                        'Enchanting' => 'Enchanting',
                        'Biome' => 'Biome',
                        'Mobs' => 'Mobs',
                        'General' => 'General',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
