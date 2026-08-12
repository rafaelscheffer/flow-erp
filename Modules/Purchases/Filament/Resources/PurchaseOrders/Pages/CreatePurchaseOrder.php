<?php

declare(strict_types=1);

namespace Modules\Purchases\Filament\Resources\PurchaseOrders\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Modules\Purchases\Enums\PurchaseOrderStatus;
use Modules\Purchases\Filament\Resources\PurchaseOrders\PurchaseOrderResource;

class CreatePurchaseOrder extends CreateRecord
{
    protected static string $resource = PurchaseOrderResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = PurchaseOrderStatus::Draft->value;
        $data['created_by'] = Auth::id();

        return $data;
    }
}
