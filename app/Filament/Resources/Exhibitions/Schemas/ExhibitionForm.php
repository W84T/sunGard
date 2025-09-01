<?php

namespace App\Filament\Resources\Exhibitions\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
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
                        ->default(fn() => auth()->id()),
                    TextInput::make('name')
                        ->label(__('exhibition.form.name'))
                        ->required(),
                    TextInput::make('discount')
                        ->label(__('exhibition.form.discount'))
                        ->required()
                        ->visible(fn ($record) => auth()->user()->can('addPlansAndDiscounts', $record))
                        ->required(),
                    TextInput::make('address')
                        ->label(__('exhibition.form.address')),
                    FileUpload::make('logo_address')
                        ->label(__('exhibition.form.logo_address'))
                        ->required()
                        ->directory('exhibition_logos')
                        ->columnSpan('full')
                        ->imageEditor(),

                    Repeater::make('plans')
                        ->label(__('exhibition.form.plans'))
                        ->visible(fn ($record) => auth()->user()->can('addPlansAndDiscounts', $record))
                        ->schema([
                            TextInput::make('value')
                                ->label(__('exhibition.form.plan_value'))
                                ->required(),
                        ])
                        ->defaultItems(1)
                        ->columnSpan('full')
                        ->reorderable()
                ])
                    ->columnSpan(2)
                    ->columns(2),
            ]);
    }
}
