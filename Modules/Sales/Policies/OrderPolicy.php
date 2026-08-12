<?php

declare(strict_types=1);

namespace Modules\Sales\Policies;

use App\Models\User;
use Modules\Sales\Models\Order;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('orders.view');
    }

    public function view(User $user, Order $order): bool
    {
        return $user->can('orders.view');
    }

    public function create(User $user): bool
    {
        return $user->can('orders.create');
    }

    public function update(User $user, Order $order): bool
    {
        return $user->can('orders.update');
    }

    public function delete(User $user, Order $order): bool
    {
        return $user->can('orders.delete');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('orders.delete');
    }

    public function confirm(User $user, Order $order): bool
    {
        return $user->can('orders.confirm');
    }

    public function invoice(User $user, Order $order): bool
    {
        return $user->can('orders.invoice');
    }
}
