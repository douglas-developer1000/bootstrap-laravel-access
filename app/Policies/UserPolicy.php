<?php

declare(strict_types=1);

namespace App\Policies;

// use Illuminate\Auth\Access\Response;
use App\Models\User;

final class UserPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        return $user->id === $model->id;
    }
}
