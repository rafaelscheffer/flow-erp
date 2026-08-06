<?php

declare(strict_types=1);

namespace Modules\Customers\Filament\Resources\Customers\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Customers\Filament\Resources\Customers\CustomerResource;

class CreateCustomer extends CreateRecord
{
    protected static string $resource = CustomerResource::class;
}
