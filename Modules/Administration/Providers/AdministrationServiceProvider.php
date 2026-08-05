<?php

declare(strict_types=1);

namespace Modules\Administration\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\Administration\Models\AuditLog;
use Modules\Administration\Policies\AuditLogPolicy;
use Modules\Administration\Policies\RolePolicy;
use Modules\Administration\Policies\UserPolicy;
use Spatie\Permission\Models\Role;

class AdministrationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');

        if (file_exists($routes = __DIR__.'/../Routes/api.php')) {
            $this->loadRoutesFrom($routes);
        }

        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(AuditLog::class, AuditLogPolicy::class);
    }
}
