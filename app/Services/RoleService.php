<?php

declare(strict_types=1);

namespace App\Services;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

final class RoleService
{
    public function createRole(string $name)
    {
        Role::create(['name' => $name]);
    }

    public function updateRole(string|int $id, string $name): void
    {
        /** @var Role $role */
        $role = Role::findOrFail($id);
        $role->update(['name' => $name]);
    }

    public function removeRole(Role $role): void
    {
        $role->delete();
    }

    public function findUnlinkedPermissionsQuery(Role $role)
    {
        return Permission::whereDoesntHave('roles', function ($query) use ($role) {
            $query->where('id', $role->id);
        });
    }

    public function bindPermissionToRole(Role $role, Permission $permission)
    {
        $role->givePermissionTo($permission);
    }

    public function unbindPermissionFromRole(Role $role, Permission $permission)
    {
        $role->revokePermissionTo($permission);
    }

    public function removeRoleList(array $ids): void
    {
        // This remotion occurs by each model, because
        // the spatie permissions package removes the roles
        // from the cache this way
        collect($ids)->each(fn($id) => Role::findById($id)->delete())->all();
    }

    public function bindPermissionGroupToRole(array $ids, Role $role): void
    {
        $permissions = Permission::whereIn('id', $ids)->get('name')->pluck('name')->all();
        $role->givePermissionTo(...$permissions);
    }

    public function unbindPermissionGroupFromRole(array $ids, Role $role)
    {
        $permissions = Permission::whereIn('id', $ids)->get('name')->pluck('name')->all();
        $role->revokePermissionTo($permissions);
    }
}
