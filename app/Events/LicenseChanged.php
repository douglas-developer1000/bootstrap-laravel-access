<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\License;
use App\Models\Plan;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class LicenseChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly Model $licensable,
        public readonly Plan $plan,
        public readonly License $license,
    ) {
        //
    }
}
