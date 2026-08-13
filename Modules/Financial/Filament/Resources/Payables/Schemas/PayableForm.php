<?php

declare(strict_types=1);

namespace Modules\Financial\Filament\Resources\Payables\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PayableForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Conta a Pagar')
                    ->columns(2)
                    ->schema([
                        Select::make('supplier_id')
                            ->label('Fornecedor')
                            ->relationship('supplier', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('purchase_order_id')
                            ->label('Pedido de Compra')
                            ->relationship('purchaseOrder', 'id')
                            ->searchable()
                            ->preload(),
                        TextInput::make('amount')
                            ->label('Valor')
                            ->numeric()
                            ->prefix('R$')
                            ->required(),
                        DatePicker::make('due_date')
                            ->label('Vencimento')
                            ->default(now())
                            ->required(),
                        TextInput::make('description')
                            ->label('Descrição')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
