<?php

declare(strict_types=1);

namespace Modules\Inventory\Policies;

use App\Models\User;
use Modules\Inventory\Models\StockBalance;

/**
 * Read-only policy — balances are a derived cache, never created/edited by
 * users directly (see StockBalance's class docblock).
 */
class StockBalancePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('balances.view');
    }

    public function view(User $user, StockBalance $stockBalance): bool
    {
        return $user->can('balances.view');
    }
}
