<?php

declare(strict_types=1);

namespace Modules\Products\Filament\Resources\Collections\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Products\Filament\Resources\Collections\ProductCollectionResource;

class ListProductCollections extends ListRecords
{
    protected static string $resource = ProductCollectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
