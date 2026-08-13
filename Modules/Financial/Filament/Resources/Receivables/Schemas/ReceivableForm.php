<?php

declare(strict_types=1);

namespace Modules\Financial\Filament\Resources\Receivables\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ReceivableForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Conta a Receber')
                    ->columns(2)
                    ->schema([
                        Select::make('customer_id')
                            ->label('Cliente')
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('order_id')
                            ->label('Pedido de Venda')
                            ->relationship('order', 'id')
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
                        Select::make('account_id')
                            ->label('Conta Contábil')
                            ->relationship('account', 'name')
                            ->searchable()
                            ->preload(),
                        Select::make('cost_center_id')
                            ->label('Centro de Custo')
                            ->relationship('costCenter', 'name')
                            ->searchable()
                            ->preload(),
                        TextInput::make('description')
                            ->label('Descrição')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
