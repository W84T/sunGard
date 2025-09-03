<?php

namespace App\Filament\Resources\Coupons\Pages;

use App\Filament\Actions\ChangeStatusAction;
use App\Filament\Resources\Coupons\CouponResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Mansoor\FilamentVersionable\Page\RevisionsAction;

class ViewCoupon extends ViewRecord
{
    protected static string $resource = CouponResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ChangeStatusAction::make()
                ->color('info'),
            EditAction::make()
                ->color('primary'),
            RevisionsAction::make()
                ->visible(fn ($record) => auth()->user()->can('revision', $record)),

            Action::make('print')
                ->label('Print')
                ->icon('heroicon-o-printer')
                ->action(fn () => 'window.print()')
                ->requiresConfirmation(false)
                ->extraAttributes(['onclick' => 'window.print()']),

        ];
    }
}
