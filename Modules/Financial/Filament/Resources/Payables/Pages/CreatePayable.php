<?php

declare(strict_types=1);

namespace Modules\Financial\Filament\Resources\Payables\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Modules\Financial\Enums\PayableStatus;
use Modules\Financial\Filament\Resources\Payables\PayableResource;

class CreatePayable extends CreateRecord
{
    protected static string $resource = PayableResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = PayableStatus::Pending->value;
        $data['created_by'] = Auth::id();

        return $data;
    }
}
