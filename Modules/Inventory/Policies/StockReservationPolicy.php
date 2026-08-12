<?php

declare(strict_types=1);

namespace Modules\Inventory\Policies;

use App\Models\User;
use Modules\Inventory\Models\StockReservation;

class StockReservationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('reservations.view');
    }

    public function view(User $user, StockReservation $stockReservation): bool
    {
        return $user->can('reservations.view');
    }

    public function create(User $user): bool
    {
        return $user->can('reservations.create');
    }

    public function update(User $user, StockReservation $stockReservation): bool
    {
        return $user->can('reservations.update');
    }
}
