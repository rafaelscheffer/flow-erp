<?php

declare(strict_types=1);

namespace Modules\Administration\Policies;

use App\Models\User;
use Modules\Administration\Models\AuditLog;

class AuditLogPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('audit-logs.view');
    }

    public function view(User $user, AuditLog $auditLog): bool
    {
        return $user->can('audit-logs.view');
    }
}
