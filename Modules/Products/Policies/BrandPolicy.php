<?php

declare(strict_types=1);

namespace Modules\Products\Policies;

use App\Models\User;
use Modules\Products\Models\Brand;

class BrandPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('brands.view');
    }

    public function view(User $user, Brand $brand): bool
    {
        return $user->can('brands.view');
    }

    public function create(User $user): bool
    {
        return $user->can('brands.create');
    }

    public function update(User $user, Brand $brand): bool
    {
        return $user->can('brands.update');
    }

    public function delete(User $user, Brand $brand): bool
    {
        return $user->can('brands.delete');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('brands.delete');
    }
}
