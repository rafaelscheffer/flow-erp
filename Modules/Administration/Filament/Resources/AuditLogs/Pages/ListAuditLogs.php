<?php

declare(strict_types=1);

namespace Modules\Administration\Filament\Resources\AuditLogs\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\Administration\Filament\Resources\AuditLogs\AuditLogResource;

class ListAuditLogs extends ListRecords
{
    protected static string $resource = AuditLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
