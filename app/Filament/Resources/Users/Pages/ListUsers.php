<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
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
                ->badge(User::whereHas('roles', fn($q) => $q->where('slug', 'admin'))
                    ->count()),

            'branch manager' => Tab::make(__('user.tabs.branch_manager'))
                ->modifyQueryUsing(fn(Builder $query) => $query->whereHas('roles', fn($q) => $q->where('slug', 'branch manager')))
                ->badge(User::whereHas('roles', fn($q) => $q->where('slug', 'branch manager'))
                    ->count()),

            'customer service manager' => Tab::make(__('user.tabs.customer_service'))
                ->modifyQueryUsing(fn(Builder $query) => $query->whereHas('roles', fn($q) => $q->where('slug', 'customer service manager')))
                ->badge(User::whereHas('roles', fn($q) => $q->where('slug', 'customer service manager'))
                    ->count()),


            'customer service' => Tab::make(__('user.tabs.employee'))
                ->modifyQueryUsing(fn(Builder $query) => $query->whereHas('roles', fn($q) => $q->where('slug', 'customer service')))
                ->badge(User::whereHas('roles', fn($q) => $q->where('slug', 'customer service'))
                    ->count()),

            'marketer' => Tab::make(__('user.tabs.marketer'))
                ->modifyQueryUsing(fn(Builder $query) => $query->whereHas('roles', fn($q) => $q->where('slug', 'marketer')))
                ->badge(User::whereHas('roles', fn($q) => $q->where('slug', 'marketer'))
                    ->count()),

            'agent' => Tab::make(__('user.tabs.agent'))
                ->modifyQueryUsing(fn(Builder $query) => $query->whereHas('roles', fn($q) => $q->where('slug', 'agent')))
                ->badge(User::whereHas('roles', fn($q) => $q->where('slug', 'agent'))
                    ->count()),

            'report manager' => Tab::make(__('user.tabs.reporter'))
                ->modifyQueryUsing(fn(Builder $query) => $query->whereHas('roles', fn($q) => $q->where('slug', 'report manager')))
                ->badge(User::whereHas('roles', fn($q) => $q->where('slug', 'report manager'))
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
            CreateAction::make()
                ->color('primary'),
        ];
    }
}
