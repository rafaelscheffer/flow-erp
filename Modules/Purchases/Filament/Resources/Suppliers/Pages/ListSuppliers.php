<?php

declare(strict_types=1);

namespace Modules\Purchases\Filament\Resources\Suppliers\Pages;

use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use Modules\Purchases\Filament\Imports\SupplierImporter;
use Modules\Purchases\Filament\Resources\Suppliers\SupplierResource;

class ListSuppliers extends ListRecords
{
    protected static string $resource = SupplierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()
                ->importer(SupplierImporter::class)
                ->visible(fn (): bool => Auth::user()?->can('suppliers.create') ?? false),
            CreateAction::make(),
        ];
    }
}
