<?php

namespace App\Filament\Resources\SungardBranches\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SungardBranchesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('created_by')
                    ->default(fn() => auth()->id()),
                TextInput::make('name')
                    ->label(__('sungard_branch.form.name'))
                    ->required(),
                ColorPicker::make('color')
                    ->label(__('sungard_branch.form.color'))
                    ->required(),
                TextInput::make('address')
                    ->label(__('sungard_branch.form.address')),
            ]);
    }
}
