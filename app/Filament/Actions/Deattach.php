<?php

namespace App\Filament\Actions;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Schmeits\FilamentPhosphorIcons\Support\Icons\Phosphor;

class Deattach
{
    public static function make(): Action
    {
        return Action::make('detach')
            ->label(__('user.action.detach'))
            ->icon(Phosphor::X)
            ->color('danger')
            ->visible(fn() => auth()->user()->can('attach_users::user'))
            ->requiresConfirmation()
            ->action(function ($record) {
                $record->update(['created_by' => null]);
            });
    }

    public static function makeBulk(): BulkAction
    {
        return BulkAction::make('detach')
            ->label(__('user.action.detach'))
            ->icon(Phosphor::X)
            ->requiresConfirmation()
            ->visible(fn() => auth()->user()->can('attach_users::user'))
            ->action(function ($records) {
                foreach ($records as $record) {
                    $record->update(['created_by' => null]);
                }
            });
    }
}
