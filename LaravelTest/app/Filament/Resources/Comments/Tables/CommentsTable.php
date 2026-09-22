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
                    ->label('For')
                    ->formatStateUsing(fn ($state) => class_basename($state))
                    ->badge(),
                TextColumn::make('commentable_id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable(),
                TextColumn::make('body')
                    ->label('Comment')
                    ->limit(50)
                    ->searchable(),
                BooleanColumn::make('approved')
                    ->label('Approved'),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->date('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('approved')
                    ->label('Approval'),
                SelectFilter::make('commentable_type')
                    ->label('Section')
                    ->options([
                        'App\Models\Server' => 'Server',
                        'App\Models\WikiPage' => 'Wiki Article',
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
