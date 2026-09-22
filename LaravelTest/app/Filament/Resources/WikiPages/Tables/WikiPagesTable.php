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
                    ->label('Title')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                TextColumn::make('category')
                    ->label('Category')
                    ->badge()
                    ->color('success')
                    ->searchable()
                    ->sortable(),
                BooleanColumn::make('approved')
                    ->label('Approved'),
                TextColumn::make('user.name')
                    ->label('Author')
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->date('d.m.Y')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('approved')
                    ->label('Approval'),
                SelectFilter::make('category')
                    ->label('Category')
                    ->options([
                        'Redstone' => 'Redstone',
                        'Farming' => 'Farming',
                        'Building' => 'Building',
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
