<?php

declare(strict_types=1);

namespace Modules\Sales\Filament\Resources\Orders;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Sales\Filament\Resources\Orders\Pages\CreateOrder;
use Modules\Sales\Filament\Resources\Orders\Pages\EditOrder;
use Modules\Sales\Filament\Resources\Orders\Pages\ListOrders;
use Modules\Sales\Filament\Resources\Orders\RelationManagers\OrderItemsRelationManager;
use Modules\Sales\Filament\Resources\Orders\Schemas\OrderForm;
use Modules\Sales\Filament\Resources\Orders\Tables\OrdersTable;
use Modules\Sales\Models\Order;
use UnitEnum;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static UnitEnum|string|null $navigationGroup = 'Vendas';

    protected static ?string $navigationLabel = 'Pedidos de Venda';

    protected static ?string $modelLabel = 'Pedido de Venda';

    protected static ?string $pluralModelLabel = 'Pedidos de Venda';

    public static function form(Schema $schema): Schema
    {
        return OrderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrdersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            OrderItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'create' => CreateOrder::route('/create'),
            'edit' => EditOrder::route('/{record}/edit'),
        ];
    }
}
