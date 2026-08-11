<?php

declare(strict_types=1);

namespace Modules\Products\Policies;

use App\Models\User;
use Modules\Products\Models\ProductCollection;

class ProductCollectionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('collections.view');
    }

    public function view(User $user, ProductCollection $productCollection): bool
    {
        return $user->can('collections.view');
    }

    public function create(User $user): bool
    {
        return $user->can('collections.create');
    }

    public function update(User $user, ProductCollection $productCollection): bool
    {
        return $user->can('collections.update');
    }

    public function delete(User $user, ProductCollection $productCollection): bool
    {
        return $user->can('collections.delete');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('collections.delete');
    }
}
