<?php

declare(strict_types=1);

namespace Modules\Inventory\Filament\Resources\StockLocations\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Inventory\Filament\Resources\StockLocations\StockLocationResource;

class EditStockLocation extends EditRecord
{
    protected static string $resource = StockLocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
