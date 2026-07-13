<?php

declare(strict_types=1);

namespace App\Models\Contracts;

use BackedEnum;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;

/**
 * Contract to ensure that model has the Spatie's roles
 */
interface HasRoleHandling
{
    /**
     * @param  string|int|array|Role|Collection|BackedEnum  ...$role
     * @return $this
     */
    public function removeRole(...$role): static;

    /**
     * Assign the given role to the model.
     *
     * @param  string|int|array|Role|Collection|BackedEnum  ...$roles
     * @return $this
     */
    public function assignRole(...$roles): static;
}
