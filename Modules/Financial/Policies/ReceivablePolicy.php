<?php

declare(strict_types=1);

namespace Modules\Financial\Policies;

use App\Models\User;
use Modules\Financial\Models\Receivable;

class ReceivablePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('receivables.view');
    }

    public function view(User $user, Receivable $receivable): bool
    {
        return $user->can('receivables.view');
    }

    public function create(User $user): bool
    {
        return $user->can('receivables.create');
    }

    public function update(User $user, Receivable $receivable): bool
    {
        return $user->can('receivables.update');
    }

    public function delete(User $user, Receivable $receivable): bool
    {
        return $user->can('receivables.delete');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('receivables.delete');
    }

    public function markAsPaid(User $user, Receivable $receivable): bool
    {
        return $user->can('receivables.mark-paid');
    }
}
