<?php

declare(strict_types=1);

namespace Modules\Financial\Filament\Resources\Receivables\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Modules\Financial\Enums\ReceivableStatus;
use Modules\Financial\Filament\Resources\Receivables\ReceivableResource;

class CreateReceivable extends CreateRecord
{
    protected static string $resource = ReceivableResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = ReceivableStatus::Pending->value;
        $data['created_by'] = Auth::id();

        return $data;
    }
}
