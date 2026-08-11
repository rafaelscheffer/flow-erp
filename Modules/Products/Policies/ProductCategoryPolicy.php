<?php

declare(strict_types=1);

namespace Modules\Products\Policies;

use App\Models\User;
use Modules\Products\Models\ProductCategory;

class ProductCategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('categories.view');
    }

    public function view(User $user, ProductCategory $productCategory): bool
    {
        return $user->can('categories.view');
    }

    public function create(User $user): bool
    {
        return $user->can('categories.create');
    }

    public function update(User $user, ProductCategory $productCategory): bool
    {
        return $user->can('categories.update');
    }

    public function delete(User $user, ProductCategory $productCategory): bool
    {
        return $user->can('categories.delete');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('categories.delete');
    }
}
