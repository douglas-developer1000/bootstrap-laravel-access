<?php

declare(strict_types=1);

namespace App\Models\Contracts;

use Spatie\Permission\Models\Role;

/**
 * Contract to ensure that model has the Spatie's roles
 */
interface HasRoleHandling
{
    public function syncRoles(array|string|Role ...$roles);
}
