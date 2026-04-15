<?php

declare(strict_types=1);

namespace App\Listeners;

// use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Auth\Events\PasswordReset;
use App\Notifications\PosResetPasswordNotification;
use App\Models\User;

final class PosResetPasswordListener
{
    /**
     * Handle the event.
     */
    public function handle(PasswordReset $event): void
    {
        /** @var User $user */
        $user = $event->user;
        $user->notify(new PosResetPasswordNotification);
    }
}
