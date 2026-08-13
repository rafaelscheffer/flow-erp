<?php

declare(strict_types=1);

namespace Modules\Financial\Policies;

use App\Models\User;
use Modules\Financial\Models\CostCenter;

class CostCenterPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('cost-centers.view');
    }

    public function view(User $user, CostCenter $costCenter): bool
    {
        return $user->can('cost-centers.view');
    }

    public function create(User $user): bool
    {
        return $user->can('cost-centers.create');
    }

    public function update(User $user, CostCenter $costCenter): bool
    {
        return $user->can('cost-centers.update');
    }

    public function delete(User $user, CostCenter $costCenter): bool
    {
        return $user->can('cost-centers.delete');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('cost-centers.delete');
    }
}
