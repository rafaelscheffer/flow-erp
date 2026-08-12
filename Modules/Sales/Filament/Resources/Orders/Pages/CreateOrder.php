<?php

declare(strict_types=1);

namespace Modules\Sales\Filament\Resources\Orders\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Modules\Sales\Enums\OrderStatus;
use Modules\Sales\Filament\Resources\Orders\OrderResource;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = OrderStatus::Draft->value;
        $data['created_by'] = Auth::id();

        return $data;
    }
}
