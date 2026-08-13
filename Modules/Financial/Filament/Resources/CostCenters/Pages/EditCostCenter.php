<?php

declare(strict_types=1);

namespace Modules\Financial\Filament\Resources\CostCenters\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Financial\Filament\Resources\CostCenters\CostCenterResource;

class EditCostCenter extends EditRecord
{
    protected static string $resource = CostCenterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
