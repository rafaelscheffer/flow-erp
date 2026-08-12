<?php

declare(strict_types=1);

namespace Modules\Inventory\Filament\Resources\StockBalances\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\Inventory\Filament\Resources\StockBalances\StockBalanceResource;

class ListStockBalances extends ListRecords
{
    protected static string $resource = StockBalanceResource::class;
}
