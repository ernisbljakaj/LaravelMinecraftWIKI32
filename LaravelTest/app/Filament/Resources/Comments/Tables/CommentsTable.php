<?php

namespace App\Filament\Resources\Comments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CommentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('commentable_type')
                    ->label('Für')
                    ->formatStateUsing(fn ($state) => class_basename($state))
                    ->badge(),
                TextColumn::make('commentable_id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Benutzer')
                    ->searchable(),
                TextColumn::make('body')
                    ->label('Kommentar')
                    ->limit(50)
                    ->searchable(),
                BooleanColumn::make('approved')
                    ->label('Freigegeben'),
                TextColumn::make('created_at')
                    ->label('Erstellt')
                    ->date('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('approved')
                    ->label('Freigabe'),
                SelectFilter::make('commentable_type')
                    ->label('Bereich')
                    ->options([
                        'App\Models\Server' => 'Server',
                        'App\Models\WikiPage' => 'Wiki-Artikel',
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
