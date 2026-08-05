<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use Modules\Administration\Providers\AdministrationServiceProvider;

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    AdministrationServiceProvider::class,
];
