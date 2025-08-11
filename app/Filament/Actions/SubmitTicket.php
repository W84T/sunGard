<?php

namespace App\Filament\Actions;

use App\Models\Ticket;
use App\Status;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class SubmitTicket
{
    public static function make(): Action
    {
        return Action::make('submit_ticket')
            ->label(__('coupon.actions.submit'))
            ->icon('heroicon-m-chat-bubble-left-ellipsis')
            ->schema([
                RichEditor::make('description')
                    ->label(__('coupon.ticket.description'))
                    ->required(),


                Select::make('priority')
                    ->label(__('coupon.ticket.priority'))
                    ->options([
                        'low' => __('coupon.ticket.priority_options.low'),
                        'medium' => __('coupon.ticket.priority_options.medium'),
                        'high' => __('coupon.ticket.priority_options.high'),
                    ])
                    ->default('low'),

                Select::make('submitted_to')
                    ->label(__('coupon.ticket.submitted_to'))
                    ->options([
                        'admin' => __('coupon.ticket.submitted_to_options.admin'),
                        'customer service manager' => __('coupon.ticket.submitted_to_options.customer_service_manager'),
                    ])
                    ->default('customer service manager'),
            ])
            ->action(function (array $data, Model $record): void {
                Ticket::create([
                    'coupon_id' => $record->id,
                    'created_by' => Auth::id(),
                    'description' => $data['description'],
                    'priority' => $data['priority'],
                    'submitted_to' => $data['submitted_to'],
                    'status' => 'open', // default value
                ]);
            })
            ->successNotificationTitle(__('coupon.notification.ticket_submit_success.title'));
    }
}
