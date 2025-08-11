<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\Branch;
use App\Models\Exhibition;
use App\Models\Role;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Collection;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('created_by')
                    ->default(fn() => auth()->id()),

                Section::make(__('user.form.user_information'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('user.form.name'))
                                    ->required(),

                                TextInput::make('email')
                                    ->label(__('user.form.email'))
                                    ->email()
                                    ->required(),

                                TextInput::make('password')
                                    ->label(__('user.form.password'))
                                    ->password()
                                    ->dehydrated(fn($state) => filled($state))
                                    ->required(fn(Page $livewire): bool => $livewire instanceof CreateRecord)
                                    ->unique(ignoreRecord: true),

                                DateTimePicker::make('email_verified_at')
                                    ->label(__('user.form.email_verified_at'))
                                    ->default(now()),
                            ]),
                    ]),

                Section::make(__('user.form.roles_associations'))
                    ->schema([
                        Select::make('roles')
                            ->label(__('user.form.roles'))
                            ->relationship('roles', 'name')
                            ->live()
                            ->multiple()
                            ->preload()
                            ->searchable(),

                        Grid::make(2)
                            ->schema([
                                Select::make('exhibition_id')
                                    ->label(__('user.form.exhibition_name'))
                                    ->options(Exhibition::all()
                                        ->pluck('name', 'id'))
                                    ->searchable()
                                    ->live()
                                    ->visible(function (Get $get) {
                                        if (empty($get('roles'))) {
                                            return false;
                                        }
                                        $roles = Role::whereIn('id', $get('roles'))
                                            ->pluck('slug')
                                            ->toArray();
                                        return in_array('agent', $roles);
                                    }),

                                Select::make('branch_id')
                                    ->label(__('user.form.branch_name'))
                                    ->options(fn(Get $get): Collection => Branch::query()
                                        ->where('exhibition_id', $get('exhibition_id'))
                                        ->pluck('name', 'id'))
                                    ->searchable()
                                    ->live()
                                    ->required(function (Get $get) {
                                        $roles = Role::whereIn('id', $get('roles') ?? [])
                                            ->pluck('slug')
                                            ->toArray();
                                        return in_array('agent', $roles);
                                    })
                                    ->visible(function (Get $get) {
                                        if (empty($get('roles'))) {
                                            return false;
                                        }
                                        $roles = Role::whereIn('id', $get('roles'))
                                            ->pluck('slug')
                                            ->toArray();
                                        return in_array('agent', $roles);
                                    }),

                                Select::make('sungard_branch_id')
                                    ->label(__('user.form.sungard_branch'))
                                    ->relationship('subgard', 'name')
                                    ->preload()
                                    ->searchable()
                                    ->live()
                                    ->columnSpan('full')
                                    ->required(function (Get $get) {
                                        $roles = Role::whereIn('id', $get('roles') ?? [])
                                            ->pluck('slug')
                                            ->toArray();
                                        return in_array('agent', $roles);
                                    })
                                    ->required(function (Get $get) {
                                        $roles = Role::whereIn('id', $get('roles') ?? [])
                                            ->pluck('slug')
                                            ->toArray();
                                        return in_array('branch-manager', $roles);
                                    })
                                    ->visible(function (Get $get) {
                                        if (empty($get('roles'))) {
                                            return false;
                                        }
                                        $roles = Role::whereIn('id', $get('roles'))
                                            ->pluck('slug')
                                            ->toArray();
                                        return in_array('branch-manager', $roles);
                                    }),
                            ]),
                    ]),
            ]);
    }
}
