<?php

declare(strict_types=1);

namespace Modules\Sales\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\Sales\Models\Order;
use Modules\Sales\Policies\OrderPolicy;

class SalesServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');

        if (file_exists($routes = __DIR__.'/../Routes/api.php')) {
            $this->loadRoutesFrom($routes);
        }

        Gate::policy(Order::class, OrderPolicy::class);
    }
}
