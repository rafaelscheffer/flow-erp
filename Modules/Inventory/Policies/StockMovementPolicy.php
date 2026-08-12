<?php

declare(strict_types=1);

namespace Modules\Inventory\Policies;

use App\Models\User;
use Modules\Inventory\Models\StockMovement;

/**
 * No update/delete/deleteAny here on purpose — movements are an immutable
 * ledger (see StockMovement's class docblock).
 */
class StockMovementPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('movements.view');
    }

    public function view(User $user, StockMovement $stockMovement): bool
    {
        return $user->can('movements.view');
    }

    public function create(User $user): bool
    {
        return $user->can('movements.create');
    }
}
