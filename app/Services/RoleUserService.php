<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

final class RoleUserService
{
    public function bindRoleToUser(User $user, Role $role): void
    {
        $user->assignRole($role);
    }

    public function unbindRoleFromUser(User $user, Role $role): void
    {
        $user->removeRole($role);
    }

    public function bindRoleGroupToUser(Request $request, User $user): void
    {
        $roles = Role::whereIn(
            'id',
            $request->validated('attachment')
        )->get('name')->pluck('name')->all();

        $user->assignRole(...$roles);
    }

    public function unbindRoleGroupFromUser(Request $request, User $user): void
    {
        $roles = Role::whereIn(
            'id',
            $request->validated('detachment')
        )->get('name')->pluck('name')->all();

        $user->removeRole(...$roles);
    }
}
