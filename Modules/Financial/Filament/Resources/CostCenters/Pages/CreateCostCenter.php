<?php

declare(strict_types=1);

namespace Modules\Financial\Filament\Resources\CostCenters\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Financial\Filament\Resources\CostCenters\CostCenterResource;

class CreateCostCenter extends CreateRecord
{
    protected static string $resource = CostCenterResource::class;
}
