<?php

declare(strict_types=1);

namespace Modules\Administration\Rules;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Spatie\Permission\Models\Role;

class RoleWithinCallerPermissions implements ValidationRule
{
    public function __construct(private readonly User $actingUser) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $role = Role::query()->where('name', $value)->first();

        if (! $role) {
            return;
        }

        $missing = $role->permissions->pluck('name')->diff($this->actingUser->getAllPermissions()->pluck('name'));

        if ($missing->isNotEmpty()) {
            $fail("Você não pode atribuir a role \"{$value}\" porque ela concede permissões que você mesmo não possui: {$missing->implode(', ')}.");
        }
    }
}
