<?php

namespace App\Filament\Resources\Servers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ServersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('ip')
                    ->label('IP')
                    ->searchable(),
                TextColumn::make('version')
                    ->label('Version')
                    ->badge()
                    ->color('gray'),
                BadgeColumn::make('mode')
                    ->label('Modus')
                    ->color('success'),
                TagsColumn::make('tags.name')
                    ->label('Tags')
                    ->limit(3),
                BooleanColumn::make('approved')
                    ->label('Freigegeben'),
                BooleanColumn::make('featured')
                    ->label('Featured'),
                TextColumn::make('created_at')
                    ->label('Erstellt')
                    ->date('d.m.Y')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('approved')
                    ->label('Freigabe'),
                SelectFilter::make('mode')
                    ->label('Modus')
                    ->options([
                        'Survival' => 'Survival',
                        'Creative' => 'Creative',
                        'PvP' => 'PvP',
                        'Skyblock' => 'Skyblock',
                        'Bedwars' => 'Bedwars',
                        'Minigames' => 'Minigames',
                        'Anarchy' => 'Anarchy',
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
