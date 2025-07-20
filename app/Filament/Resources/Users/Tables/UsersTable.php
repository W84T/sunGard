<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('creator.name')
                    ->label(__('user.table.creator_name'))
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('user.table.name'))
                    ->searchable(),
                TextColumn::make('email')
                    ->label(__('user.table.email'))
                    ->copyable()
                    ->searchable(),
                TextColumn::make('roles.name')
                    ->label(__('user.table.roles'))
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'super_admin' => 'danger',
                        'admin' => 'primary',
                        default => 'success',
                    })
                    ->formatStateUsing(fn(string $state): string => str($state)
                        ->replace('_', ' ')
                        ->title()),
                TextColumn::make('email_verified_at')
                    ->label(__('user.table.email_verified_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault:  true),
                TextColumn::make('created_at')
                    ->label(__('user.table.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('user.table.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
