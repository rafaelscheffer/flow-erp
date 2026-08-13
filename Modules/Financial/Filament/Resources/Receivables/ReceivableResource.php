<?php

declare(strict_types=1);

namespace Modules\Financial\Filament\Resources\Receivables;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Financial\Filament\Resources\Receivables\Pages\CreateReceivable;
use Modules\Financial\Filament\Resources\Receivables\Pages\EditReceivable;
use Modules\Financial\Filament\Resources\Receivables\Pages\ListReceivables;
use Modules\Financial\Filament\Resources\Receivables\Schemas\ReceivableForm;
use Modules\Financial\Filament\Resources\Receivables\Tables\ReceivablesTable;
use Modules\Financial\Models\Receivable;
use UnitEnum;

class ReceivableResource extends Resource
{
    protected static ?string $model = Receivable::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static UnitEnum|string|null $navigationGroup = 'Financeiro';

    protected static ?string $navigationLabel = 'Contas a Receber';

    protected static ?string $modelLabel = 'Conta a Receber';

    protected static ?string $pluralModelLabel = 'Contas a Receber';

    public static function form(Schema $schema): Schema
    {
        return ReceivableForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReceivablesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReceivables::route('/'),
            'create' => CreateReceivable::route('/create'),
            'edit' => EditReceivable::route('/{record}/edit'),
        ];
    }
}
