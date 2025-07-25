<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('created_by')
                    ->default(fn () => auth()->id()),
                TextInput::make('name')
                    ->label(__('user.form.name'))
                    ->required(),
                TextInput::make('email')
                    ->label(__('user.form.email'))
                    ->email()
                    ->required(),
                Select::make('roles')
                    ->label(__('user.form.roles'))
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
                DateTimePicker::make('email_verified_at')
                    ->label(__('user.form.email_verified_at'))
                    ->default(now()),
                TextInput::make('password')
                    ->label(__('user.form.password'))
                    ->password()
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (Page $livewire): bool => $livewire instanceof CreateRecord)
                    ->unique(ignoreRecord: true),
            ]);
    }
}
