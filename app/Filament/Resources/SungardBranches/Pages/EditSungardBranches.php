<?php

namespace App\Filament\Resources\SungardBranches\Pages;

use App\Filament\Resources\SungardBranches\SungardBranchesResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSungardBranches extends EditRecord
{
    protected static string $resource = SungardBranchesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
