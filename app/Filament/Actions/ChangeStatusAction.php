<?php

namespace App\Filament\Actions;

use App\Status;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Model;

class ChangeStatusAction
{
    public static function make(): Action
    {
        return Action::make('change_status')
            ->label(__('Change Status'))
            ->icon('heroicon-o-pencil-square')
            ->form([
                Select::make('status')
                    ->label(__('New Status'))
                    ->options(Status::options())
                    ->required(),
            ])
            ->action(function (array $data, Model $record): void {
                $record->update([
                    'status' => $data['status'],
                ]);
            })
            ->successNotificationTitle('تم تحديث الحالة بنجاح!');
    }
}
