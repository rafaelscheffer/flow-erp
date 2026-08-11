<?php

declare(strict_types=1);

namespace Modules\Products\Filament\Resources\Collections\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Products\Filament\Resources\Collections\ProductCollectionResource;

class EditProductCollection extends EditRecord
{
    protected static string $resource = ProductCollectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
