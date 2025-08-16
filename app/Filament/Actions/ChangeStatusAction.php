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
        return Action::make(__('coupon.action.change_status'))
            ->icon('heroicon-o-pencil-square')
            ->visible(function ($record) {
                $user = auth()->user();

                if (!$user->hasRoleSlug('customer service')) {
                    return false;
                }

                $status = $record->status;
                if ($status === null) {
                    return false;
                }
                // Normalize status into enum
                $status = $record->status instanceof Status
                    ? $status
                    : Status::tryFrom((int)$record->status);

                // 1. No status → no action
                if (!$status) {
                    return false;
                }

                // 2. Reserved → always allow
                if ($status->isReserved()) {
                    return true;
                }

                // 3. Scheduled → only allow if confirmed
                if ($status->isScheduled()) {
                    return (bool)$record->is_confirmed;
                }

                // 4. Everything else (Booked, NotBooked, etc.) → allow
                return true;
            })
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
