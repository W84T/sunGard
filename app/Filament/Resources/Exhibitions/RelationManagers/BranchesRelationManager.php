<?php

namespace App\Filament\Resources\Exhibitions\RelationManagers;

use App\Filament\Resources\Branches\BranchResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class BranchesRelationManager extends RelationManager
{
    protected static string $relationship = 'Branches';

    protected static ?string $relatedResource = BranchResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
