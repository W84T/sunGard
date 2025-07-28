<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use App\Models\Role;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Collection;
use App\Models\Exhibition;
use App\Models\Branch;
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
                    ->live()
                    ->multiple()
                    ->preload()
                    ->searchable(),
                Select::make('exhibition_id')
                    ->label('Exhibition')
                    ->options(Exhibition::all()->pluck('name', 'id'))
                    ->searchable()
                    ->live()
                    ->visible(function (Get $get) {
                        if (empty($get('roles'))) {
                            return false;
                        }
                        $roles = Role::whereIn('id', $get('roles'))->pluck('name')->toArray();
                        return in_array('agent', $roles);
                    }),
                Select::make('branch_id')
                    ->label('Branch')
                    ->options(fn (Get $get): Collection => Branch::query()
                        ->where('exhibition_id', $get('exhibition_id'))
                        ->pluck('name', 'id'))
                    ->searchable()
                    ->live()
                    ->visible(function (Get $get) {
                        if (empty($get('roles'))) {
                            return false;
                        }
                        $roles = Role::whereIn('id', $get('roles'))->pluck('name')->toArray();
                        return in_array('agent', $roles);
                    }),
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
