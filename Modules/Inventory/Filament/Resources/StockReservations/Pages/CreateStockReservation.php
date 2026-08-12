<?php

declare(strict_types=1);

namespace Modules\Inventory\Filament\Resources\StockReservations\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Modules\Inventory\Filament\Resources\StockReservations\StockReservationResource;
use Modules\Inventory\Models\StockBalance;

class CreateStockReservation extends CreateRecord
{
    protected static string $resource = StockReservationResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $data['reserved_by'] = auth()->id();

        $available = StockBalance::query()
            ->where('product_id', $data['product_id'])
            ->where('product_variant_id', $data['product_variant_id'] ?? null)
            ->where('stock_location_id', $data['stock_location_id'])
            ->first()?->available_quantity ?? 0;

        if ($data['quantity'] > $available) {
            throw ValidationException::withMessages([
                'quantity' => "Quantidade indisponível para reserva. Disponível: {$available}.",
            ]);
        }

        return static::getModel()::create($data);
    }
}
