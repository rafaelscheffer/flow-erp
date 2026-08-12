<?php

declare(strict_types=1);

namespace Modules\Inventory\Policies;

use App\Models\User;
use Modules\Inventory\Models\StockLocation;

class StockLocationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('locations.view');
    }

    public function view(User $user, StockLocation $stockLocation): bool
    {
        return $user->can('locations.view');
    }

    public function create(User $user): bool
    {
        return $user->can('locations.create');
    }

    public function update(User $user, StockLocation $stockLocation): bool
    {
        return $user->can('locations.update');
    }

    public function delete(User $user, StockLocation $stockLocation): bool
    {
        return $user->can('locations.delete');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('locations.delete');
    }
}
