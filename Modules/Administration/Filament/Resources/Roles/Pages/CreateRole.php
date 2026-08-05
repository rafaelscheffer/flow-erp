<?php

declare(strict_types=1);

namespace Modules\Administration\Filament\Resources\Roles\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Administration\Filament\Resources\Roles\RoleResource;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;
}
