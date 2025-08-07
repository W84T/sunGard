<?php

namespace App\Filament\Actions;

use App\Status;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Model;

class ChangeStatusAction
{
    public static function make(): Action
    {
        return Action::make('change_status')
            ->icon('heroicon-o-pencil-square')
            ->visible(function ($record) {
                $user = auth()->user();

                $isVisibleToEmployee = $record->employee_id && $user?->roles->contains('slug', 'employee');

                $status = $record->status;

                if (!$status) {
                    return false;
                }

                $isNotReserved = !$status->isReserved();
                $isNotScheduled = !$status->isScheduled();

                return $isVisibleToEmployee
                    && $isNotReserved
                    && $isNotScheduled
                    && $record->is_confirmed;
            })
            ->schema(fn(Action $action): array => [
                Select::make('status')
                    ->label(__('New Status'))
                    ->options(Status::optionsExcept([Status::RESERVED]))
                    ->default($action->getRecord()->status)
                    ->live()
                    ->required(),

                DateTimePicker::make('reserved_date')
                    ->label(__('coupon.form.reserved_date'))
                    ->visible(fn(Get $get) => $get('status') === Status::BOOKED)
                    ->required(fn(Get $get) => $get('status') === Status::BOOKED),
                Select::make('sungard_branch_id')
                    ->label(__('coupon.form.sungard_branch_name'))
                    ->relationship('sungard', 'name')
                    ->visible(fn(Get $get) => $get('status') === Status::BOOKED)
                    ->required(fn(Get $get) => $get('status') === Status::BOOKED),
            ])
            ->action(function (array $data, Model $record): void {
                $record->update([
                    'status' => $data['status'],
                ]);
            })
            ->successNotificationTitle('تم تحديث الحالة بنجاح!');
    }
}
