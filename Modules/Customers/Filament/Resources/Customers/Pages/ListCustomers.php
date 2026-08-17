<?php

declare(strict_types=1);

namespace Modules\Customers\Filament\Resources\Customers\Pages;

use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use Modules\Customers\Filament\Imports\CustomerImporter;
use Modules\Customers\Filament\Resources\Customers\CustomerResource;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()
                ->importer(CustomerImporter::class)
                ->visible(fn (): bool => Auth::user()?->can('customers.create') ?? false),
            CreateAction::make(),
        ];
    }
}
