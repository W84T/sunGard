<?php

namespace App\Filament\Resources\Exhibitions\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ExhibitionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
              Section::make([
                  Hidden::make('created_by')
                      ->default(fn () => auth()->id()),
                  TextInput::make('name')
                      ->label(__('exhibition.form.name'))
                      ->required(),
                  TextInput::make('address')
                      ->label(__('exhibition.form.address')),
              ])->columnSpan(2)->columns(2),
            ]);
    }
}
