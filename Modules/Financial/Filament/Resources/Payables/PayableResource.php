<?php

declare(strict_types=1);

namespace Modules\Financial\Filament\Resources\Payables;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Financial\Filament\Resources\Payables\Pages\CreatePayable;
use Modules\Financial\Filament\Resources\Payables\Pages\EditPayable;
use Modules\Financial\Filament\Resources\Payables\Pages\ListPayables;
use Modules\Financial\Filament\Resources\Payables\Schemas\PayableForm;
use Modules\Financial\Filament\Resources\Payables\Tables\PayablesTable;
use Modules\Financial\Models\Payable;
use UnitEnum;

class PayableResource extends Resource
{
    protected static ?string $model = Payable::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedReceiptPercent;

    protected static UnitEnum|string|null $navigationGroup = 'Financeiro';

    protected static ?string $navigationLabel = 'Contas a Pagar';

    protected static ?string $modelLabel = 'Conta a Pagar';

    protected static ?string $pluralModelLabel = 'Contas a Pagar';

    public static function form(Schema $schema): Schema
    {
        return PayableForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PayablesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayables::route('/'),
            'create' => CreatePayable::route('/create'),
            'edit' => EditPayable::route('/{record}/edit'),
        ];
    }
}
