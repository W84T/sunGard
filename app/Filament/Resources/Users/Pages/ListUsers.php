<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Coupons\CouponResource;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use App\Status;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    public function getTabs(): array
    {
        return [
            'admin' => Tab::make(__('user.tabs.admin'))
                ->modifyQueryUsing(fn(Builder $query) => $query->whereHas('roles', fn($q) => $q->where('slug', 'admin')))
                ->badge(\App\Models\User::whereHas('roles', fn($q) => $q->where('slug', 'admin'))
                    ->count()),

            'agent' => Tab::make(__('user.tabs.agent'))
                ->modifyQueryUsing(fn(Builder $query) => $query->whereHas('roles', fn($q) => $q->where('slug', 'agent')))
                ->badge(\App\Models\User::whereHas('roles', fn($q) => $q->where('slug', 'agent'))
                    ->count()),

            'marketer' => Tab::make(__('user.tabs.marketer'))
                ->modifyQueryUsing(fn(Builder $query) => $query->whereHas('roles', fn($q) => $q->where('slug', 'marketer')))
                ->badge(\App\Models\User::whereHas('roles', fn($q) => $q->where('slug', 'marketer'))
                    ->count()),

            'employee' => Tab::make(__('user.tabs.employee'))
                ->modifyQueryUsing(fn(Builder $query) => $query->whereHas('roles', fn($q) => $q->where('slug', 'employee')))
                ->badge(\App\Models\User::whereHas('roles', fn($q) => $q->where('slug', 'employee'))
                    ->count()),

            'reporter' => Tab::make(__('user.tabs.reporter'))
                ->modifyQueryUsing(fn(Builder $query) => $query->whereHas('roles', fn($q) => $q->where('slug', 'reporter')))
                ->badge(\App\Models\User::whereHas('roles', fn($q) => $q->where('slug', 'reporter'))
                    ->count()),

            'branch manager' => Tab::make(__('user.tabs.branch_manager'))
                ->modifyQueryUsing(fn(Builder $query) => $query->whereHas('roles', fn($q) => $q->where('slug', 'branch manager')))
                ->badge(\App\Models\User::whereHas('roles', fn($q) => $q->where('slug', 'branch manager'))
                    ->count()),

        ];
    }


    public function getDefaultActiveTab(): string|int|null
    {
        return 'admin';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
