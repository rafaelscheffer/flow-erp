<?php

declare(strict_types=1);

namespace Modules\Purchases\Filament\Resources\Suppliers\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Purchases\Filament\Resources\Suppliers\SupplierResource;

class CreateSupplier extends CreateRecord
{
    protected static string $resource = SupplierResource::class;
}
