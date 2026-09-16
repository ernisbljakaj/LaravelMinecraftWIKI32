<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('E-Mail')
                    ->searchable(),
                BadgeColumn::make('role')
                    ->label('Rolle')
                    ->formatStateUsing(fn ($state) => $state === 'admin' ? 'Administrator' : 'Benutzer')
                    ->color(fn ($state) => $state === 'admin' ? 'warning' : 'gray'),
                TextColumn::make('servers_count')
                    ->label('Server')
                    ->counts('servers')
                    ->sortable(),
                TextColumn::make('comments_count')
                    ->label('Kommentare')
                    ->counts('comments')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Registriert')
                    ->date('d.m.Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('Rolle')
                    ->options([
                        'user' => 'Benutzer',
                        'admin' => 'Administrator',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
