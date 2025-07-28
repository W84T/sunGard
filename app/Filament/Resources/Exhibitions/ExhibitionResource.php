<?php

namespace App\Filament\Resources\Exhibitions;

use App\Filament\Resources\Exhibitions\Pages\ManageExhibitions;
use App\Filament\Resources\Exhibitions\RelationManagers\BranchRelationManager;
use App\Models\Exhibition;
use BackedEnum;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Guava\FilamentModalRelationManagers\Actions\RelationManagerAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Mansoor\FilamentVersionable\Table\RevisionsAction;

class ExhibitionResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Exhibition::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::BuildingStorefront;

    public static function getModelLabel(): string
    {
        return __('exhibition.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('exhibition.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('created_by')
                    ->default(fn () => auth()->id()),
                TextInput::make('name')
                    ->label(__('exhibition.form.name'))
                    ->required(),
                TextInput::make('address')
                    ->label(__('exhibition.form.address')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('creator.name')
                    ->label(__('exhibition.table.creator_name'))
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('exhibition.table.name'))
                    ->searchable(),
                TextColumn::make('address')
                    ->label(__('exhibition.table.address'))
                    ->searchable(),
                TextColumn::make('deleted_at')
                    ->label(__('exhibition.table.deleted_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('exhibition.table.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('exhibition.table.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
               ActionGroup::make([
                   EditAction::make()
                       ->color('primary'),
                   DeleteAction::make()
                       ->color('danger'),
                   ForceDeleteAction::make()
                       ->color('danger'),
                   RestoreAction::make()
                       ->color('success'),
                   //                RevisionsAction::make(),
               ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->color('danger'),
                    ForceDeleteBulkAction::make()
                        ->color('danger'),
                    RestoreBulkAction::make()
                        ->color('success'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageExhibitions::route('/'),
            //            'revisions' => Pages\ExhibitionRevisions::route('/{record}/revisions'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
        ];
    }
}
