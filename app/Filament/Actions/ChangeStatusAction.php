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
            ->icon('heroicon-o-pencil-square')
            ->visible(function ($record) {
                $user = auth()->user();
                $isVisible = $record->employee_id && $user?->roles->contains('slug', 'employee');

                return $isVisible;
            })
            ->schema(fn (Action $action): array => [
                Select::make('status')
                    ->label(__('New Status'))
                    ->options(Status::optionsExcept([Status::RESERVED]))
                    ->default($action->getRecord()->status)
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
