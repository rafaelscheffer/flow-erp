<?php

declare(strict_types=1);

namespace Modules\Financial\Filament\Resources\Accounts\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Financial\Filament\Resources\Accounts\AccountResource;

class CreateAccount extends CreateRecord
{
    protected static string $resource = AccountResource::class;
}
