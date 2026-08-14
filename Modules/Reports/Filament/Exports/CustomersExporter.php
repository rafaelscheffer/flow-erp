<?php

declare(strict_types=1);

namespace Modules\Reports\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Modules\Customers\Enums\CustomerType;
use Modules\Customers\Models\Customer;

class CustomersExporter extends BaseExporter
{
    protected static ?string $model = Customer::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')->label('Nome'),
            ExportColumn::make('type')
                ->label('Tipo')
                ->formatStateUsing(fn (CustomerType $state): string => $state->label()),
            ExportColumn::make('document')->label('Documento'),
            ExportColumn::make('city')->label('Cidade'),
            ExportColumn::make('state')->label('Estado'),
            ExportColumn::make('phone')->label('Telefone'),
            ExportColumn::make('email')->label('E-mail'),
            ExportColumn::make('is_active')->label('Ativo'),
        ];
    }
}
