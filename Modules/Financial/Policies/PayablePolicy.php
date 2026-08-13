<?php

declare(strict_types=1);

namespace Modules\Financial\Policies;

use App\Models\User;
use Modules\Financial\Models\Payable;

class PayablePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('payables.view');
    }

    public function view(User $user, Payable $payable): bool
    {
        return $user->can('payables.view');
    }

    public function create(User $user): bool
    {
        return $user->can('payables.create');
    }

    public function update(User $user, Payable $payable): bool
    {
        return $user->can('payables.update');
    }

    public function delete(User $user, Payable $payable): bool
    {
        return $user->can('payables.delete');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('payables.delete');
    }

    public function markAsPaid(User $user, Payable $payable): bool
    {
        return $user->can('payables.mark-paid');
    }
}
