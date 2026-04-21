<?php

declare(strict_types=1);

namespace App\Libraries\Traits;

use Spatie\Permission\Models\Permission;

trait EagerPermissionTrait
{
    public function makePermissions(string ...$permissionNames): void
    {
        collect($permissionNames)->each(
            fn(string $name) => Permission::findOrCreate($name)
        );
    }

    public function clearPermissions(string ...$permissionNames): void
    {
        Permission::whereIn('name', $permissionNames)->delete();
    }
}
