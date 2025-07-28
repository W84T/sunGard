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
            'admin' => Tab::make('Admins')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereHas('roles', fn($q) => $q->where('slug', 'admin')))
                ->badge(\App\Models\User::whereHas('roles', fn($q) => $q->where('slug', 'admin'))
                    ->count()),

            'agent' => Tab::make('Agents')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereHas('roles', fn($q) => $q->where('slug', 'agent')))
                ->badge(\App\Models\User::whereHas('roles', fn($q) => $q->where('slug', 'agent'))
                    ->count()),

            'marketer' => Tab::make('Marketers')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereHas('roles', fn($q) => $q->where('slug', 'marketer')))
                ->badge(\App\Models\User::whereHas('roles', fn($q) => $q->where('slug', 'marketer'))
                    ->count()),

            'employee' => Tab::make('Employees')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereHas('roles', fn($q) => $q->where('slug', 'employee')))
                ->badge(\App\Models\User::whereHas('roles', fn($q) => $q->where('slug', 'employee'))
                    ->count()),

            'reporter' => Tab::make('Reporter')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereHas('roles', fn($q) => $q->where('slug', 'reporter')))
                ->badge(\App\Models\User::whereHas('roles', fn($q) => $q->where('slug', 'reporter'))
                    ->count()),

            'branch manager' => Tab::make('Branch Manager')
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
