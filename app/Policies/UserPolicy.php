<?php

declare(strict_types=1);

namespace App\Policies;

use App\Libraries\Enums\PermissionNameEnum;
use App\Models\User;
// use Illuminate\Auth\Access\Response;

final class UserPolicy
{
    public function beSuperAdmin(User $user): bool
    {
        return $user->hasRole('super-admin');
    }

    /**
     * Determine whether the user can show the model.
     * @see ../../routes/custom/settings.php
     */
    public function show(User $user): bool
    {
        return $user->can(PermissionNameEnum::HEADER_SETTINGS);
    }

    /**
     * Determine whether the user can edit the model.
     * @see ../../routes/custom/settings.php
     */
    public function edit(User $user): bool
    {
        return $user->can(PermissionNameEnum::HEADER_SETTINGS);
    }

    /**
     * Determine whether the user can update the model.
     * @see ../../routes/custom/settings.php
     */
    public function update(User $user, User $model): bool
    {
        return (
            $user->id === $model->id &&
            $user->can(PermissionNameEnum::HEADER_SETTINGS)
        );
    }
}
