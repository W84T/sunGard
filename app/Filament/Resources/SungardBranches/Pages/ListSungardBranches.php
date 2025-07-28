<?php

namespace App\Filament\Resources\SungardBranches\Pages;

use App\Filament\Resources\SungardBranches\SungardBranchesResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSungardBranches extends ListRecords
{
    protected static string $resource = SungardBranchesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
