<?php

declare(strict_types=1);

namespace Modules\Products\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\Products\Models\Brand;
use Modules\Products\Models\Product;
use Modules\Products\Models\ProductCategory;
use Modules\Products\Models\ProductCollection;
use Modules\Products\Policies\BrandPolicy;
use Modules\Products\Policies\ProductCategoryPolicy;
use Modules\Products\Policies\ProductCollectionPolicy;
use Modules\Products\Policies\ProductPolicy;

class ProductsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');

        if (file_exists($routes = __DIR__.'/../Routes/api.php')) {
            $this->loadRoutesFrom($routes);
        }

        Gate::policy(Brand::class, BrandPolicy::class);
        Gate::policy(ProductCategory::class, ProductCategoryPolicy::class);
        Gate::policy(ProductCollection::class, ProductCollectionPolicy::class);
        Gate::policy(Product::class, ProductPolicy::class);
    }
}
