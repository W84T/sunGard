<?php

namespace App\Filament\Actions;

use App\Models\Coupon;
use App\Models\SungardBranch; // ✅ singular, adjust if your model is plural
use App\Models\SungardBranches;
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
            ->visible(fn ($record) => auth()->user()->can('changeStatus', $record))
            ->schema(fn (Action $action): array => [

                // Status dropdown
                Select::make('status')
                    ->label(__('coupon.status.new_status'))
                    ->options(Status::optionsExcept([Status::RESERVED]))
                    ->default($action->getRecord()->status)
                    ->live()
                    ->required(),

                // Reservation date
                DateTimePicker::make('reserved_date')
                    ->label(__('coupon.form.reserved_date'))
                    ->default($action->getRecord()->reserved_date)
                    ->visible(fn (Get $get) => $get('status') === Status::BOOKED)
                    ->required(fn (Get $get) => $get('status') === Status::BOOKED),

                // Branch selection with capacity validation
                Select::make('sungard_branch_id')
                    ->label(__('coupon.form.sungard_branch_name'))
                    ->relationship('sungard', 'name')
                    ->default(fn (Action $action) => $action->getRecord()?->sungard_branch_id) // safe null
                    ->visible(fn (Get $get) => $get('status') === Status::BOOKED)
                    ->required(fn (Get $get) => $get('status') === Status::BOOKED)
                    ->rule(function (callable $get) {
                        return function (string $attribute, $value, \Closure $fail) use ($get) {
                            if ($get('status') !== Status::BOOKED) {
                                return; // Only validate when booking
                            }

                            $reservedDate = $get('reserved_date');
                            if (! $reservedDate || ! $value) {
                                return; // Skip if data incomplete
                            }

                            $branch = SungardBranches::find($value);
                            if (! $branch) {
                                return;
                            }

                            $bookingsCount = Coupon::query()
                                ->where('sungard_branch_id', $value)
                                ->whereDate('reserved_date', \Carbon\Carbon::parse($reservedDate)->toDateString())
                                ->where('status', Status::BOOKED)
                                ->count();

                            if ($bookingsCount >= $branch->max_capacity) {
                                $fail(__('coupon.notification.capacity_reached', [
                                    'date' => \Carbon\Carbon::parse($reservedDate)->format('Y-m-d'),
                                    'branch' => $branch->name,
                                    'capacity' => $branch->max_capacity,
                                ]));
                            }
                        };
                    }),
            ])
            ->action(function (array $data, Model $record): void {
                // ✅ Final safeguard (in case of bypassing form rules)
                if ($data['status'] === Status::BOOKED) {
                    $reservedDate = $data['reserved_date'] ?? null;
                    $branchId = $data['sungard_branch_id'] ?? null;

                    if ($reservedDate && $branchId) {
                        $branch = SungardBranches::find($branchId);

                        $bookingsCount = Coupon::query()
                            ->where('sungard_branch_id', $branchId)
                            ->whereDate('reserved_date', \Carbon\Carbon::parse($reservedDate)->toDateString())
                            ->where('status', Status::BOOKED)
                            ->count();

                        if ($branch && $bookingsCount >= $branch->max_capacity) {
                            throw \Filament\Support\Exceptions\Halt::make(
                                __('coupon.notification.capacity_reached', [
                                    'date' => \Carbon\Carbon::parse($reservedDate)->format('Y-m-d'),
                                    'branch' => $branch->name,
                                    'capacity' => $branch->max_capacity,
                                ])
                            );
                        }
                    }
                }

                // Update record
                $record->update([
                    'status' => $data['status'],
                    'reserved_date' => $data['reserved_date'] ?? $record->reserved_date,
                    'sungard_branch_id' => $data['sungard_branch_id'] ?? $record->sungard_branch_id,
                ]);
            })
            ->successNotificationTitle(__('coupon.notification.status_update_success.title'));
    }
}
