<?php

declare(strict_types=1);

namespace Modules\Inventory\Filament\Resources\StockLocations\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Inventory\Filament\Resources\StockLocations\StockLocationResource;

class CreateStockLocation extends CreateRecord
{
    protected static string $resource = StockLocationResource::class;
}
