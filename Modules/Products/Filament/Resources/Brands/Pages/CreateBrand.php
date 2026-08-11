<?php

declare(strict_types=1);

namespace Modules\Products\Filament\Resources\Brands\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Products\Filament\Resources\Brands\BrandResource;

class CreateBrand extends CreateRecord
{
    protected static string $resource = BrandResource::class;
}
