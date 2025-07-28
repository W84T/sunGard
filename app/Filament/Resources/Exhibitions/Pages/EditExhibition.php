<?php

namespace App\Filament\Resources\Exhibitions\Pages;

use App\Filament\Resources\Exhibitions\ExhibitionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditExhibition extends EditRecord
{
    protected static string $resource = ExhibitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->color('danger'),
            ForceDeleteAction::make()
                ->color('danger'),
            RestoreAction::make()
                ->color('success'),
        ];
    }
}
