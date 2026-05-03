<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

final class PermissionUserService
{
    public function bindDirectPermissionToUser(User $user, Permission $permission)
    {
        $user->givePermissionTo($permission);
    }

    public function unbindDirectPermissionToUser(User $user, Permission $permission)
    {
        $user->revokePermissionTo($permission);
    }

    public function bindDirectPermissionGroupToUser(Request $request, User $user)
    {
        $permissions = Permission::whereIn(
            'id',
            $request->validated('attachment')
        )->get('name')->pluck('name')->all();

        $user->givePermissionTo(...$permissions);
    }

    public function unbindDirectPermissionGroupToUser(Request $request, User $user)
    {
        $permissions = Permission::whereIn(
            'id',
            $request->validated('detachment')
        )->get('name')->pluck('name')->all();

        $user->revokePermissionTo($permissions);
    }
}
