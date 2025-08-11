<?php

namespace App\Filament\Resources\Branches\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class BranchesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('creator.name')
                    ->label(__('branch.table.creator_name'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('exhibition.name')
                    ->label(__('branch.table.exhibition_name')),
                TextColumn::make('name')
                    ->label(__('branch.table.name'))
                    ->searchable(),
                TextColumn::make('address')
                    ->label(__('branch.table.address'))
                    ->searchable(),
                TextColumn::make('deleted_at')
                    ->label(__('branch.table.deleted_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('branch.table.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('branch.table.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make()
                    ->color('primary'),
                DeleteAction::make()
                    ->color('danger'),
                ForceDeleteAction::make()
                    ->color('danger'),
                RestoreAction::make()
                    ->color('success'),
                //                RevisionsAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->color('danger'),
                    ForceDeleteBulkAction::make()
                        ->color('danger'),
                    RestoreBulkAction::make()
                        ->color('success'),
                ]),
            ]);
    }
}
