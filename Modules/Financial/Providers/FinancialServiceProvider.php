<?php

declare(strict_types=1);

namespace Modules\Financial\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\Financial\Listeners\GeneratePayableForReceivedPurchaseOrder;
use Modules\Financial\Listeners\GenerateReceivableForConfirmedOrder;
use Modules\Financial\Models\Payable;
use Modules\Financial\Models\Receivable;
use Modules\Financial\Policies\PayablePolicy;
use Modules\Financial\Policies\ReceivablePolicy;
use Modules\Purchases\Events\PurchaseOrderReceived;
use Modules\Sales\Events\OrderConfirmed;

class FinancialServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');

        if (file_exists($routes = __DIR__.'/../Routes/api.php')) {
            $this->loadRoutesFrom($routes);
        }

        Gate::policy(Receivable::class, ReceivablePolicy::class);
        Gate::policy(Payable::class, PayablePolicy::class);

        Event::listen(OrderConfirmed::class, GenerateReceivableForConfirmedOrder::class);
        Event::listen(PurchaseOrderReceived::class, GeneratePayableForReceivedPurchaseOrder::class);
    }
}
