<?php

declare(strict_types=1);

namespace Modules\Financial\Filament\Resources\CostCenters\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Financial\Filament\Resources\CostCenters\CostCenterResource;

class ListCostCenters extends ListRecords
{
    protected static string $resource = CostCenterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
