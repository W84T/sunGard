<?php

namespace App\Filament\Actions;

use App\Status;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ChangeStatusAction
{
    public static function make(): Action
    {
        $user = Auth::user();
        return Action::make(__('coupon.action.change_status'))
            ->icon('heroicon-o-pencil-square')
            ->visible(fn($record) => auth()
                ->user()
                ->can('changeStatus', $record))
            ->schema(fn(Action $action): array => [
                Select::make('status')
                    ->label(__('coupon.status.new_status'))
                    ->options(Status::optionsExcept([Status::RESERVED]))
                    ->default($action->getRecord()->status)
                    ->live()
                    ->required(),

                DateTimePicker::make('reserved_date')
                    ->label(__('coupon.form.reserved_date'))
                    ->default($action->getRecord()->reserved_date)
                    ->visible(fn(Get $get) => $get('status') === Status::BOOKED)
                    ->required(fn(Get $get) => $get('status') === Status::BOOKED),
                Select::make('sungard_branch_id')
                    ->label(__('coupon.form.sungard_branch_name'))
                    ->relationship('sungard', 'name')
                    ->default($action->getRecord()->sungard_branch_id)
                    ->visible(fn(Get $get) => $get('status') === Status::BOOKED)
                    ->required(fn(Get $get) => $get('status') === Status::BOOKED),
            ])
            ->action(function (array $data, Model $record): void {
                $record->update([
                    'status' => $data['status'],
                    'reserved_date' => $data['reserved_date'] ?? $record->reserved_date,
                    'sungard_branch_id' => $data['sungard_branch_id'] ?? $record->sungard_branch_id,
                ]);
            })
            ->successNotificationTitle(__('coupon.notification.status_update_success.title'));
    }
}
