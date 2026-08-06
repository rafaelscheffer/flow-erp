<?php

declare(strict_types=1);

namespace Modules\Customers\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\Customers\Models\Customer;
use Modules\Customers\Policies\CustomerPolicy;

class CustomersServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');

        if (file_exists($routes = __DIR__.'/../Routes/api.php')) {
            $this->loadRoutesFrom($routes);
        }

        Gate::policy(Customer::class, CustomerPolicy::class);
    }
}
