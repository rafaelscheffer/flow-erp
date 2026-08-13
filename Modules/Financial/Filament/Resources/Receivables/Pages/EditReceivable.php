<?php

declare(strict_types=1);

namespace Modules\Financial\Filament\Resources\Receivables\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Financial\Filament\Resources\Receivables\ReceivableResource;

class EditReceivable extends EditRecord
{
    protected static string $resource = ReceivableResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
