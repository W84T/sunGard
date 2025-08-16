<?php

namespace App\Filament\Resources\Coupons\Resources\Tickets\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class TicketsTable
{
    public static function configure(Table $table): Table
    {
        $user = Auth::user();
        return $table
            ->columns(
                collect([
                    TextColumn::make('created_by')
                        ->label(__('coupon.ticket.created_by'))
                        ->numeric()
                        ->sortable(),

                    TextColumn::make('closed_by')
                        ->label(__('coupon.ticket.closed_by'))
                        ->numeric()
                        ->sortable(),

                    TextColumn::make('title')
                        ->label(__('coupon.ticket.title'))
                        ->searchable(),

                    auth()
                        ->user()
                        ->hasAnyRoleSlug(['admin', 'customer service manager'])
                        ? SelectColumn::make('status')
                        ->options([
                            'open' => __('Open'),
                            'closed' => __('Closed'),
                        ])
                        ->sortable()
                        ->label(__('coupon.ticket.status'))
                        : TextColumn::make('status')
                        ->label(__('coupon.ticket.status'))
                        ->icon(fn(string $state): string => match ($state) {
                            'open' => 'heroicon-o-clock',
                            'closed' => 'heroicon-o-check-circle',
                            default => 'heroicon-o-question-mark-circle',
                        })
                        ->color(fn(string $state): string => match ($state) {
                            'open' => 'info',
                            'closed' => 'success',
                            default => 'gray',
                        }),

                    TextColumn::make('priority')
                        ->label(__('coupon.ticket.priority'))
                        ->badge(),

                    TextColumn::make('submitted_to')
                        ->label(__('coupon.ticket.submitted_to')),

                    TextColumn::make('closed_at')
                        ->label(__('coupon.ticket.closed_at'))
                        ->dateTime()
                        ->sortable(),

                    TextColumn::make('created_at')
                        ->label(__('coupon.ticket.created_at'))
                        ->dateTime()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),

                    TextColumn::make('updated_at')
                        ->label(__('coupon.ticket.updated_at'))
                        ->dateTime()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
                ])
                    ->filter() // removes nulls if condition returns false
                    ->all()
            )
            ->filters([
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
