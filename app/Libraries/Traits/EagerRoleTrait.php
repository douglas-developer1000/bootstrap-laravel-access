<?php

declare(strict_types=1);

namespace App\Libraries\Traits;

use Spatie\Permission\Models\Role;

trait EagerRoleTrait
{
    public function makeRoles(string ...$roleNames): void
    {
        collect($roleNames)->each(
            fn(string $name) => Role::findOrCreate($name)
        );
    }

    public function clearRoles(string ...$roleNames): void
    {
        Role::whereIn('name', $roleNames)->delete();
    }
}
