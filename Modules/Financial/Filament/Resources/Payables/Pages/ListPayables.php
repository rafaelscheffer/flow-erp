<?php

declare(strict_types=1);

namespace Modules\Financial\Filament\Resources\Payables\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Financial\Filament\Resources\Payables\PayableResource;

class ListPayables extends ListRecords
{
    protected static string $resource = PayableResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
