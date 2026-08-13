<?php

declare(strict_types=1);

namespace Modules\Financial\Filament\Resources\Payables\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Financial\Filament\Resources\Payables\PayableResource;

class EditPayable extends EditRecord
{
    protected static string $resource = PayableResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
