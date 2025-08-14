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

        $counts = User::query()
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_type', User::class) // 👈 ensure only users
            ->selectRaw("
        SUM(roles.slug = 'admin') AS admin,
        SUM(roles.slug = 'branch manager') AS branch_manager,
        SUM(roles.slug = 'customer service manager') AS customer_service_manager,
        SUM(roles.slug = 'customer service') AS customer_service,
        SUM(roles.slug = 'marketer') AS marketer,
        SUM(roles.slug = 'agent') AS agent,
        SUM(roles.slug = 'report manager') AS report_manager
    ")
            ->first()
            ->toArray();


        return [
            'admin' => Tab::make(__('user.tabs.admin'))
                ->modifyQueryUsing(fn(Builder $query) => $query->whereHas('roles', fn($q) => $q->where('slug', 'admin')))
                ->badge($counts['admin']),

            'branch manager' => Tab::make(__('user.tabs.branch_manager'))
                ->modifyQueryUsing(fn(Builder $query) => $query->whereHas('roles', fn($q) => $q->where('slug', 'branch manager')))
                ->badge($counts['branch_manager']),

            'customer service manager' => Tab::make(__('user.tabs.customer_service'))
                ->modifyQueryUsing(fn(Builder $query) => $query->whereHas('roles', fn($q) => $q->where('slug', 'customer service manager')))
                ->badge($counts['customer_service_manager']),


            'customer service' => Tab::make(__('user.tabs.employee'))
                ->modifyQueryUsing(fn(Builder $query) => $query->whereHas('roles', fn($q) => $q->where('slug', 'customer service')))
                ->badge($counts['customer_service']),

            'marketer' => Tab::make(__('user.tabs.marketer'))
                ->modifyQueryUsing(fn(Builder $query) => $query->whereHas('roles', fn($q) => $q->where('slug', 'marketer')))
                ->badge($counts['marketer']),

            'agent' => Tab::make(__('user.tabs.agent'))
                ->modifyQueryUsing(fn(Builder $query) => $query->whereHas('roles', fn($q) => $q->where('slug', 'agent')))
                ->badge($counts['agent']),

            'report manager' => Tab::make(__('user.tabs.reporter'))
                ->modifyQueryUsing(fn(Builder $query) => $query->whereHas('roles', fn($q) => $q->where('slug', 'report manager')))
                ->badge($counts['report_manager']),


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
