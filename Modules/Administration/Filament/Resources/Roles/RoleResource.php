<?php

declare(strict_types=1);

namespace Modules\Administration\Filament\Resources\Roles;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Administration\Filament\Resources\Roles\Pages\CreateRole;
use Modules\Administration\Filament\Resources\Roles\Pages\EditRole;
use Modules\Administration\Filament\Resources\Roles\Pages\ListRoles;
use Modules\Administration\Filament\Resources\Roles\Schemas\RoleForm;
use Modules\Administration\Filament\Resources\Roles\Tables\RolesTable;
use Spatie\Permission\Models\Role;
use UnitEnum;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static UnitEnum|string|null $navigationGroup = 'Administração';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $modelLabel = 'Papel';

    protected static ?string $pluralModelLabel = 'Papéis';

    public static function form(Schema $schema): Schema
    {
        return RoleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RolesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRoles::route('/'),
            'create' => CreateRole::route('/create'),
            'edit' => EditRole::route('/{record}/edit'),
        ];
    }
}
