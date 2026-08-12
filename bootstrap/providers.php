<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use Modules\Administration\Providers\AdministrationServiceProvider;
use Modules\Customers\Providers\CustomersServiceProvider;
use Modules\Inventory\Providers\InventoryServiceProvider;
use Modules\Products\Providers\ProductsServiceProvider;
use Modules\Purchases\Providers\PurchasesServiceProvider;
use Modules\Sales\Providers\SalesServiceProvider;

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    AdministrationServiceProvider::class,
    CustomersServiceProvider::class,
    ProductsServiceProvider::class,
    InventoryServiceProvider::class,
    PurchasesServiceProvider::class,
    SalesServiceProvider::class,
];
