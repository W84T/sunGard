<?php

namespace App\Filament\Resources\Branches\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BranchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('created_by')
                    ->default(fn () => auth()->id()),
                Select::make('exhibition_id')
                    ->label(__('branch.form.exhibition_name'))
                    ->relationship('exhibition', 'name')
                    ->required(),
                TextInput::make('name')
                    ->label(__('branch.form.name'))
                    ->required(),
                Textarea::make('address')
                    ->columnSpan('full')
                    ->label(__('branch.form.address')),
            ]);
    }
}
