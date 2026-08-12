<?php

declare(strict_types=1);

namespace Modules\Administration\Filament\Resources\Users;

use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Administration\Filament\Resources\Users\Pages\CreateUser;
use Modules\Administration\Filament\Resources\Users\Pages\EditUser;
use Modules\Administration\Filament\Resources\Users\Pages\ListUsers;
use Modules\Administration\Filament\Resources\Users\Schemas\UserForm;
use Modules\Administration\Filament\Resources\Users\Tables\UsersTable;
use UnitEnum;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static UnitEnum|string|null $navigationGroup = 'Administração';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $modelLabel = 'Usuário';

    protected static ?string $pluralModelLabel = 'Usuários';

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
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
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
