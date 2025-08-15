<?php

namespace App\Filament\Resources\SungardBranches\Schemas;

use Awcodes\Palette\Forms\Components\ColorPicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;

class SungardBranchesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('created_by')
                    ->default(fn () => auth()->id()),
                TextInput::make('name')
                    ->label(__('sungard_branch.form.name'))
                    ->required(),
                //                ColorPicker::make('color')
                //
                TextInput::make('address')
                    ->label(__('sungard_branch.form.address')),

                ColorPicker::make('color')
                    ->label(__('sungard_branch.form.color'))
                    ->required()
                    ->unique()
                    ->colors([
                        '#e23636' => Color::hex('#e23636'),
                        '#e25f36' => Color::hex('#e25f36'),
                        '#e28836' => Color::hex('#e28836'),
                        '#e2b236' => Color::hex('#e2b236'),
                        '#e2db36' => Color::hex('#e2db36'),
                        '#bfe236' => Color::hex('#bfe236'),
                        '#96e236' => Color::hex('#96e236'),
                        '#6de236' => Color::hex('#6de236'),
                        '#43e236' => Color::hex('#43e236'),
                        '#36e251' => Color::hex('#36e251'),
                        '#36e27b' => Color::hex('#36e27b'),
                        '#36e2a4' => Color::hex('#36e2a4'),
                        '#36e2cd' => Color::hex('#36e2cd'),
                        '#36cde2' => Color::hex('#36cde2'),
                        '#36a4e2' => Color::hex('#36a4e2'),
                        '#367be2' => Color::hex('#367be2'),
                        '#3651e2' => Color::hex('#3651e2'),
                        '#4336e2' => Color::hex('#4336e2'),
                        '#6d36e2' => Color::hex('#6d36e2'),
                        '#9636e2' => Color::hex('#9636e2'),
                        '#bf36e2' => Color::hex('#bf36e2'),
                        '#e236db' => Color::hex('#e236db'),
                        '#e236b2' => Color::hex('#e236b2'),
                        '#e23688' => Color::hex('#e23688'),
                        '#e2365f' => Color::hex('#e2365f'),

                    ])
                    ->storeAsKey()
                    ->shades([
                        'badass' => 300,
                    ])
                    ->labels([
                        'bg-gradient-secondary' => 'Gradient Secondary',
                    ])
                    ->size('lg'),

            ]);
    }
}
