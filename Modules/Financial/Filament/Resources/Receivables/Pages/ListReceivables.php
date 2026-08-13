<?php

declare(strict_types=1);

namespace Modules\Financial\Filament\Resources\Receivables\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Financial\Filament\Resources\Receivables\ReceivableResource;

class ListReceivables extends ListRecords
{
    protected static string $resource = ReceivableResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
