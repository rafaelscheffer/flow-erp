<?php

declare(strict_types=1);

namespace Modules\Products\Filament\Resources\Collections\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Products\Filament\Resources\Collections\ProductCollectionResource;

class CreateProductCollection extends CreateRecord
{
    protected static string $resource = ProductCollectionResource::class;
}
