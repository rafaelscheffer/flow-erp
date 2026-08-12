<?php

declare(strict_types=1);

namespace Modules\Inventory\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\Inventory\Models\StockBalance;
use Modules\Inventory\Models\StockLocation;
use Modules\Inventory\Models\StockMovement;
use Modules\Inventory\Models\StockReservation;
use Modules\Inventory\Observers\StockMovementObserver;
use Modules\Inventory\Observers\StockReservationObserver;
use Modules\Inventory\Policies\StockBalancePolicy;
use Modules\Inventory\Policies\StockLocationPolicy;
use Modules\Inventory\Policies\StockMovementPolicy;
use Modules\Inventory\Policies\StockReservationPolicy;

class InventoryServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');

        if (file_exists($routes = __DIR__.'/../Routes/api.php')) {
            $this->loadRoutesFrom($routes);
        }

        Gate::policy(StockLocation::class, StockLocationPolicy::class);
        Gate::policy(StockMovement::class, StockMovementPolicy::class);
        Gate::policy(StockBalance::class, StockBalancePolicy::class);
        Gate::policy(StockReservation::class, StockReservationPolicy::class);

        StockMovement::observe(StockMovementObserver::class);
        StockReservation::observe(StockReservationObserver::class);
    }
}
