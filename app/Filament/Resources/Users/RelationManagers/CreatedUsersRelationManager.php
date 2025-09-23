<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Filament\Actions\Deattach;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Schmeits\FilamentPhosphorIcons\Support\Icons\Phosphor;

class CreatedUsersRelationManager extends RelationManager
{
    protected static string $relationship = 'createdUsers';

    protected static ?string $relatedResource = UserResource::class;

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {

        if ($ownerRecord->hasAnyRoleSlug(['admin', 'customer service manager', 'marketer'])) {
            return true;
        }

        return false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->label(__('user.table.user')),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('branch.name')
                    ->label(__('user.table.branch'))
                    ->toggleable(),
                TextColumn::make('exhibition.name')
                    ->label(__('user.table.exhibition'))
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->headerActions([
                Action::make('attach_user')
                    ->label(__('user.action.attach'))
                    ->icon(Phosphor::Paperclip)
                    ->visible(fn() => auth()->user()->can('Attach:User'))
                    ->color('gray')
                    ->schema(function () {
                        $owner = $this->getOwnerRecord();
                        return [
                            Select::make('user_id')
                                ->multiple()
                                ->label('User')
                                ->options(function () use ($owner) {
                                    if (! $owner) {
                                        return collect();
                                    }

                                    $query = User::query()->whereNull('created_by');
                                    $slugs = [];

                                    // Collect slugs based on owner role(s)
                                    if ($owner->hasRoleSlug('manager of customer service manager')) {
                                        $slugs[] = 'customer service manager';
                                    }

                                    if ($owner->hasRoleSlug('customer service manager')) {
                                        $slugs[] = 'customer service';
                                    }

                                    if ($owner->hasRoleSlug('marketer')) {
                                        $slugs[] = 'agent';
                                    }

                                    if ($owner->hasRoleSlug('admin')) {
                                        $slugs = array_merge($slugs, [
                                            'admin',
                                            'manager of customer service manager',
                                            'customer service manager',
                                            'report manager',
                                            'branch manager',
                                            'marketer',
                                        ]);
                                    }

                                    // Remove duplicates if any
                                    $slugs = array_unique($slugs);

                                    // If no roles matched, return empty
                                    if (empty($slugs)) {
                                        return collect();
                                    }

                                    // Final query
                                    return $query
                                        ->whereHas('roles', fn($q) => $q->whereIn('slug', $slugs))
                                        ->with('roles') // eager load roles
                                        ->get()
                                        ->mapWithKeys(function ($user) {
                                            $roleNames = $user->roles->pluck('name')->toArray();
                                            $label = $user->name . ' (' . implode(', ', $roleNames) . ')';
                                            return [$user->id => $label];
                                        });
                                })
                        ];
                    })
                    ->action(function (array $data) {
                        $owner = $this->getOwnerRecord();
                        if (! $owner) return;

                        if (! empty($data['user_id'])) {
                            User::whereIn('id', $data['user_id'])
                                ->update(['created_by' => $owner->id]);
                        }
                    }),

            ])
            ->recordActions([
                Deattach::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    Deattach::makeBulk(),
                ]),
            ]);
    }
}
