<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('E-Mail')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique('users', 'email', ignoreRecord: true),

                Select::make('role')
                    ->label('Rolle')
                    ->options([
                        'user' => 'Benutzer',
                        'admin' => 'Administrator',
                    ])
                    ->default('user')
                    ->required(),

                TextInput::make('password')
                    ->label('Passwort')
                    ->password()
                    ->revealable()
                    ->minLength(8)
                    ->same('passwordConfirmation')
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $operation) => $operation === 'create'),

                TextInput::make('passwordConfirmation')
                    ->label('Passwort bestätigen')
                    ->password()
                    ->revealable()
                    ->minLength(8)
                    ->dehydrated(false),
            ]);
    }
}
