<?php

namespace App\Filament\Resources\SungardBranches\Schemas;

//use Awcodes\Palette\Forms\Components\ColorPicker;/
use Filament\Forms\Components\ColorPicker;
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
                //
                TextInput::make('address')
                    ->label(__('sungard_branch.form.address')),

                ColorPicker::make('color')
                    ->label(__('sungard_branch.form.color'))
                    ->required()
                    ->unique(),

//                ColorPicker::make('color')
//                    ->label(__('sungard_branch.form.color'))
//                    ->required()
//                    ->unique()
//                    ->colors([
//                        '#e6194b' => Color::hex('#e6194b'), // red
//                        '#3cb44b' => Color::hex('#3cb44b'), // green
//                        '#ffe119' => Color::hex('#ffe119'), // yellow
//                        '#4363d8' => Color::hex('#4363d8'), // blue
//                        '#f58231' => Color::hex('#f58231'), // orange
//                        '#911eb4' => Color::hex('#911eb4'), // purple
//                        '#46f0f0' => Color::hex('#46f0f0'), // cyan
//                        '#f032e6' => Color::hex('#f032e6'), // magenta
//                        '#bcf60c' => Color::hex('#bcf60c'), // lime
//                        '#fabebe' => Color::hex('#fabebe'), // light pink
//
//                        '#008080' => Color::hex('#008080'), // teal
//                        '#e6beff' => Color::hex('#e6beff'), // lavender
//                        '#9a6324' => Color::hex('#9a6324'), // brown
//                        '#fffac8' => Color::hex('#fffac8'), // beige
//                        '#800000' => Color::hex('#800000'), // maroon
//                        '#aaffc3' => Color::hex('#aaffc3'), // mint
//                        '#808000' => Color::hex('#808000'), // olive
//                        '#ffd8b1' => Color::hex('#ffd8b1'), // apricot
//                        '#000075' => Color::hex('#000075'), // navy
//                        '#808080' => Color::hex('#808080'), // gray
//
//                        '#ffe4e1' => Color::hex('#ffe4e1'), // misty rose
//                        '#7fffd4' => Color::hex('#7fffd4'), // aquamarine
//                        '#deb887' => Color::hex('#deb887'), // burlywood
//                        '#20b2aa' => Color::hex('#20b2aa'), // light sea green
//                        '#ff6347' => Color::hex('#ff6347'), // tomato
//                        '#6a5acd' => Color::hex('#6a5acd'), // slate blue
//                        '#daa520' => Color::hex('#daa520'), // goldenrod
//                        '#ff69b4' => Color::hex('#ff69b4'), // hot pink
//                        '#cd5c5c' => Color::hex('#cd5c5c'), // indian red
//                        '#00ced1' => Color::hex('#00ced1'), // dark turquoise
//
//                        '#ffd700' => Color::hex('#ffd700'), // gold
//                        '#adff2f' => Color::hex('#adff2f'), // green yellow
//                        '#1e90ff' => Color::hex('#1e90ff'), // dodger blue
//                        '#ba55d3' => Color::hex('#ba55d3'), // orchid
//                        '#ff4500' => Color::hex('#ff4500'), // orange red
//                        '#6495ed' => Color::hex('#6495ed'), // cornflower blue
//                        '#ffdab9' => Color::hex('#ffdab9'), // peach puff
//                        '#dc143c' => Color::hex('#dc143c'), // crimson
//                        '#7fff00' => Color::hex('#7fff00'), // chartreuse
//                        '#40e0d0' => Color::hex('#40e0d0'), // turquoise
//
//                        '#f0e68c' => Color::hex('#f0e68c'), // khaki
//                        '#ff1493' => Color::hex('#ff1493'), // deep pink
//                        '#00fa9a' => Color::hex('#00fa9a'), // spring green
//                        '#b22222' => Color::hex('#b22222'), // firebrick
//                        '#9932cc' => Color::hex('#9932cc'), // dark orchid
//                        '#ff8c00' => Color::hex('#ff8c00'), // dark orange
//                        '#4682b4' => Color::hex('#4682b4'), // steel blue
//                        '#9acd32' => Color::hex('#9acd32'), // yellow green
//                        '#ffb6c1' => Color::hex('#ffb6c1'), // light pink
//                        '#2e8b57' => Color::hex('#2e8b57'), // sea green
//                    ])
//
//                    ->storeAsKey()
//                    ->shades([
//                        'badass' => 300,
//                    ])
//                    ->labels([
//                        'bg-gradient-secondary' => 'Gradient Secondary',
//                    ])
//                    ->size('lg'),

            ]);
    }
}
