<?php

declare(strict_types=1);

namespace Modules\Inventory\Filament\Resources\StockReservations\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Inventory\Filament\Resources\StockReservations\StockReservationResource;

class ListStockReservations extends ListRecords
{
    protected static string $resource = StockReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
