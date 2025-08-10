<?php

namespace App\Filament\Resources\SungardBranches;

use App\Filament\Resources\SungardBranches\Pages\CreateSungardBranches;
use App\Filament\Resources\SungardBranches\Pages\EditSungardBranches;
use App\Filament\Resources\SungardBranches\Pages\ListSungardBranches;
use App\Filament\Resources\SungardBranches\Schemas\SungardBranchesForm;
use App\Filament\Resources\SungardBranches\Tables\SungardBranchesTable;
use App\Models\SungardBranches;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Schmeits\FilamentPhosphorIcons\Support\Icons\Phosphor;
use Schmeits\FilamentPhosphorIcons\Support\Icons\PhosphorWeight;

class SungardBranchesResource extends Resource
{
    protected static ?string $model = SungardBranches::class;
    protected static ?int $navigationSort = 4;

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return Phosphor::CarSimple->getIconForWeight(PhosphorWeight::Regular);
    }
    public static function getActiveNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return Phosphor::CarSimple->getIconForWeight(PhosphorWeight::Duotone);
    }

    public static function getModelLabel(): string
    {
        return __('sungard_branch.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('sungard_branch.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return SungardBranchesForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SungardBranchesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\UsersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSungardBranches::route('/'),
            'create' => CreateSungardBranches::route('/create'),
            'edit' => EditSungardBranches::route('/{record}/edit'),
        ];
    }
}
