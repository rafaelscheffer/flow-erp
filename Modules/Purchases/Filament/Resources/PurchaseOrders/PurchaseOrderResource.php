<?php

declare(strict_types=1);

namespace Modules\Purchases\Filament\Resources\PurchaseOrders;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Purchases\Filament\Resources\PurchaseOrders\Pages\CreatePurchaseOrder;
use Modules\Purchases\Filament\Resources\PurchaseOrders\Pages\EditPurchaseOrder;
use Modules\Purchases\Filament\Resources\PurchaseOrders\Pages\ListPurchaseOrders;
use Modules\Purchases\Filament\Resources\PurchaseOrders\RelationManagers\PurchaseOrderItemsRelationManager;
use Modules\Purchases\Filament\Resources\PurchaseOrders\Schemas\PurchaseOrderForm;
use Modules\Purchases\Filament\Resources\PurchaseOrders\Tables\PurchaseOrdersTable;
use Modules\Purchases\Models\PurchaseOrder;
use UnitEnum;

class PurchaseOrderResource extends Resource
{
    protected static ?string $model = PurchaseOrder::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingCart;

    protected static UnitEnum|string|null $navigationGroup = 'Compras';

    protected static ?string $navigationLabel = 'Pedidos de Compra';

    protected static ?string $modelLabel = 'Pedido de Compra';

    protected static ?string $pluralModelLabel = 'Pedidos de Compra';

    public static function form(Schema $schema): Schema
    {
        return PurchaseOrderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PurchaseOrdersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            PurchaseOrderItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPurchaseOrders::route('/'),
            'create' => CreatePurchaseOrder::route('/create'),
            'edit' => EditPurchaseOrder::route('/{record}/edit'),
        ];
    }
}
