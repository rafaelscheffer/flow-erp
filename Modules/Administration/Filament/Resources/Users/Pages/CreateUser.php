<?php

declare(strict_types=1);

namespace Modules\Administration\Filament\Resources\Users\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Administration\Filament\Resources\Users\UserResource;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
