<?php

declare(strict_types=1);

namespace Modules\Financial\Policies;

use App\Models\User;
use Modules\Financial\Models\Account;

class AccountPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('accounts.view');
    }

    public function view(User $user, Account $account): bool
    {
        return $user->can('accounts.view');
    }

    public function create(User $user): bool
    {
        return $user->can('accounts.create');
    }

    public function update(User $user, Account $account): bool
    {
        return $user->can('accounts.update');
    }

    public function delete(User $user, Account $account): bool
    {
        return $user->can('accounts.delete');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('accounts.delete');
    }
}
