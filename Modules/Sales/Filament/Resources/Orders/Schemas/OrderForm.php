<?php

declare(strict_types=1);

namespace Modules\Sales\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Sales\Enums\PaymentMethod;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Pedido')
                    ->columns(2)
                    ->schema([
                        Select::make('customer_id')
                            ->label('Cliente')
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('stock_location_id')
                            ->label('Local de Saída')
                            ->relationship('location', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        DatePicker::make('order_date')
                            ->label('Data do Pedido')
                            ->default(now())
                            ->required(),
                        Select::make('payment_method')
                            ->label('Forma de Pagamento')
                            ->options(collect(PaymentMethod::cases())->mapWithKeys(
                                fn (PaymentMethod $case): array => [$case->value => $case->label()],
                            ))
                            ->required(),
                        TextInput::make('discount_amount')
                            ->label('Desconto')
                            ->numeric()
                            ->prefix('R$')
                            ->default(0)
                            ->required(),
                        TextInput::make('shipping_amount')
                            ->label('Frete')
                            ->numeric()
                            ->prefix('R$')
                            ->default(0)
                            ->required(),
                        Textarea::make('notes')
                            ->label('Observações')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
