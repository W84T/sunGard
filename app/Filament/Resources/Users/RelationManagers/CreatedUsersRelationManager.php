<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\DetachAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class CreatedUsersRelationManager extends RelationManager
{
    protected static string $relationship = 'createdUsers';

    protected static ?string $relatedResource = UserResource::class;


    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->label('User'),
                TextColumn::make('email')->searchable(),
                TextColumn::make('branch.name')->label('Branch')->toggleable(),
                TextColumn::make('exhibition.name')->label('Exhibition')->toggleable(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->recordActions([
               DetachAction::make(),

               DeleteAction::make(),
            ]);
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {

        if ($ownerRecord->hasAnyRoleSlug(['admin', 'customer service manager', 'marketer'])) {
            return true;
        }

        return false;
    }
}
