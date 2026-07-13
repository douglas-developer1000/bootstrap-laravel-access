<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Contracts\Licensable;
use App\Models\License;
use App\Models\Plan;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

final class LicenseExpired
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly Licensable $licensable,
        public readonly Plan $plan,
        public readonly License $license,
    ) {
        $link = route('licenses.show', ['license' => $license->id], true);
        $email = $licensable->getBillingEmail();

        Log::channel('slack')->info(
            Str::of(
                "Licensa expirada: {$link}\n"
            )
                ->append(
                    "- Plano: {$plan->name}\n",
                    "- Email: {$email}"
                )
        );
    }
}
