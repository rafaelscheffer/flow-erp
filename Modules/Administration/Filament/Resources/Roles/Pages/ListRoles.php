<?php

declare(strict_types=1);

namespace Modules\Administration\Filament\Resources\Roles\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Administration\Filament\Resources\Roles\RoleResource;

class ListRoles extends ListRecords
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
