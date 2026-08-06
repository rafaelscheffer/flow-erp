<?php

declare(strict_types=1);

namespace Modules\Customers\Policies;

use App\Models\User;
use Modules\Customers\Models\Customer;

class CustomerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('customers.view');
    }

    public function view(User $user, Customer $customer): bool
    {
        return $user->can('customers.view');
    }

    public function create(User $user): bool
    {
        return $user->can('customers.create');
    }

    public function update(User $user, Customer $customer): bool
    {
        return $user->can('customers.update');
    }

    public function delete(User $user, Customer $customer): bool
    {
        return $user->can('customers.delete');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('customers.delete');
    }
}
