<?php

declare(strict_types=1);

namespace Modules\Customers\Filament\Resources\Customers;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Customers\Filament\Resources\Customers\Pages\CreateCustomer;
use Modules\Customers\Filament\Resources\Customers\Pages\EditCustomer;
use Modules\Customers\Filament\Resources\Customers\Pages\ListCustomers;
use Modules\Customers\Filament\Resources\Customers\Schemas\CustomerForm;
use Modules\Customers\Filament\Resources\Customers\Tables\CustomersTable;
use Modules\Customers\Models\Customer;
use UnitEnum;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    protected static UnitEnum|string|null $navigationGroup = 'Cadastros';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $modelLabel = 'Cliente';

    protected static ?string $pluralModelLabel = 'Clientes';

    public static function form(Schema $schema): Schema
    {
        return CustomerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CustomersTable::configure($table);
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
            'index' => ListCustomers::route('/'),
            'create' => CreateCustomer::route('/create'),
            'edit' => EditCustomer::route('/{record}/edit'),
        ];
    }
}
