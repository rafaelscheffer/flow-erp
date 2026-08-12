<?php

declare(strict_types=1);

namespace Modules\Inventory\Filament\Resources\StockMovements\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Modules\Inventory\Enums\StockMovementType;
use Modules\Inventory\Filament\Resources\StockMovements\StockMovementResource;

class CreateStockMovement extends CreateRecord
{
    protected static string $resource = StockMovementResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $type = StockMovementType::from($data['type']);
        $quantity = (int) $data['quantity'];

        $data['quantity'] = match ($type) {
            StockMovementType::Entrada => abs($quantity),
            StockMovementType::Saida => -abs($quantity),
            default => $quantity,
        };

        $data['performed_by'] = auth()->id();

        return static::getModel()::create($data);
    }
}
