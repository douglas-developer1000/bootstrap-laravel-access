<?php

declare(strict_types=1);

namespace App\Policies;

use App\Http\Controllers\LicenseController;
use App\Models\License;
use App\Models\User;
use App\Services\LicenseStates\LicenseCanceledState;
use App\Services\LicenseStates\LicensePendingState;

final class LicensePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return
            $user->can('beSuperAdmin', User::class) &&
            ! $user->deleted_at;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, License $license): bool
    {
        return
            $user->can('beSuperAdmin', User::class) &&
            $user->is($license->licensable);
    }

    /**
     * @see LicenseCanceledState::activateLicense()
     * @see LicensePendingState::activateLicense()
     * @see LicenseController::activate()
     */
    public function activate(User $user, License $license): bool
    {
        return $user->can('beSuperAdmin', User::class) && (
            $license->isActivatable ||
            $license->isReactivatable
        );
    }

    public function cancel(User $user, License $license): bool
    {
        return (
            $user->can('beSuperAdmin', User::class) ||
            $user->is($license->licensable)
        ) && $license->isPreCancellable;
    }
}
