<?php

declare(strict_types=1);

namespace Modules\Purchases\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\Purchases\Models\PurchaseOrder;
use Modules\Purchases\Models\Supplier;
use Modules\Purchases\Policies\PurchaseOrderPolicy;
use Modules\Purchases\Policies\SupplierPolicy;

class PurchasesServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');

        if (file_exists($routes = __DIR__.'/../Routes/api.php')) {
            $this->loadRoutesFrom($routes);
        }

        Gate::policy(Supplier::class, SupplierPolicy::class);
        Gate::policy(PurchaseOrder::class, PurchaseOrderPolicy::class);
    }
}
