<?php

declare(strict_types=1);

namespace Modules\Financial\Filament\Resources\Accounts;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Financial\Filament\Resources\Accounts\Pages\CreateAccount;
use Modules\Financial\Filament\Resources\Accounts\Pages\EditAccount;
use Modules\Financial\Filament\Resources\Accounts\Pages\ListAccounts;
use Modules\Financial\Filament\Resources\Accounts\Schemas\AccountForm;
use Modules\Financial\Filament\Resources\Accounts\Tables\AccountsTable;
use Modules\Financial\Models\Account;
use UnitEnum;

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static UnitEnum|string|null $navigationGroup = 'Financeiro';

    protected static ?string $navigationLabel = 'Plano de Contas';

    protected static ?string $modelLabel = 'Conta Contábil';

    protected static ?string $pluralModelLabel = 'Plano de Contas';

    public static function form(Schema $schema): Schema
    {
        return AccountForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AccountsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAccounts::route('/'),
            'create' => CreateAccount::route('/create'),
            'edit' => EditAccount::route('/{record}/edit'),
        ];
    }
}
