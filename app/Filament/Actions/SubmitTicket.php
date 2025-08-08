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
                    ->label('وصف التذكرة')
                    ->required(),


                Select::make('priority')
                    ->label('الأولوية')
                    ->options([
                        'low' => 'منخفضة',
                        'medium' => 'متوسطة',
                        'high' => 'مرتفعة',
                    ])
                    ->default('low'),

                Select::make('submitted_to')
                    ->label('إرسال إلى')
                    ->options([
                        'admin' => 'الإدارة',
                        'customer service manager' => 'مدير خدمة العملاء',
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
            ->successNotificationTitle('تم إرسال التذكرة بنجاح!');
    }
}
