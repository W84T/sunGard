<?php

namespace App\Filament\Resources\Exhibitions;

use App\Filament\Resources\Exhibitions\Pages\CreateExhibition;
use App\Filament\Resources\Exhibitions\Pages\EditExhibition;
use App\Filament\Resources\Exhibitions\Pages\ListExhibitions;
use App\Filament\Resources\Exhibitions\Pages\ManageExhibitions;
use App\Filament\Resources\Exhibitions\RelationManagers\BranchRelationManager;
use App\Filament\Resources\Exhibitions\Schemas\ExhibitionForm;
use App\Filament\Resources\Exhibitions\Tables\ExhibitionsTable;
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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Schmeits\FilamentPhosphorIcons\Support\Icons\Phosphor;
use Schmeits\FilamentPhosphorIcons\Support\Icons\PhosphorWeight;

class ExhibitionResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Exhibition::class;
    protected static ?int $navigationSort = 2;

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return Phosphor::BuildingOffice->getIconForWeight(PhosphorWeight::Regular);
    }

    public static function getActiveNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return Phosphor::BuildingOffice->getIconForWeight(PhosphorWeight::Duotone);
    }

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
        return ExhibitionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExhibitionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExhibitions::route('/'),
            'create' => CreateExhibition::route('/create'),
            'edit' => EditExhibition::route('/{record}/edit'),
        ];
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
