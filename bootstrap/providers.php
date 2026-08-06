<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use Modules\Administration\Providers\AdministrationServiceProvider;
use Modules\Customers\Providers\CustomersServiceProvider;

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    AdministrationServiceProvider::class,
    CustomersServiceProvider::class,
];
