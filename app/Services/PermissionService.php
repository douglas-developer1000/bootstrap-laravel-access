<?php

declare(strict_types=1);

namespace App\Services;

use Spatie\Permission\Models\Permission;

final class PermissionService
{
    public function createPermission(string $name)
    {
        Permission::create(['name' => $name]);
    }

    public function updatePermission(Permission $permission, string $name): void
    {
        $permission->update(['name' => $name]);
    }

    public function removePermission(Permission $permission): void
    {
        $permission->delete();
    }

    public function removePermissionList(array $ids): void
    {
        // This remotion occurs by each model, because
        // the spatie permissions package removes the permissions
        // from the cache this way
        collect($ids)->each(fn($id) => Permission::findById($id)->delete());
    }
}
