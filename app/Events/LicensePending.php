<?php

declare(strict_types=1);

namespace App\Events;

use App\Libraries\Traits\EmailTranspHandlerTrait;
use App\Mail\LicensePendingMail;
use App\Models\Contracts\Licensable;
use App\Models\License;
use App\Models\Plan;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

final class LicensePending
{
    use Dispatchable, EmailTranspHandlerTrait, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly Licensable $licensable,
        public readonly Plan $plan,
        public readonly License $license,
    ) {
        $this->notifySlack($licensable, $plan, $license);
        $this->sendLicensableEmail($licensable, $plan);
    }

    protected function notifySlack(Licensable $licensable, Plan $plan, License $license): void
    {
        $link = route('licenses.show', ['license' => $license->id], true);
        $email = $licensable->getBillingEmail();

        Log::channel('slack')->info(
            Str::of(
                "Licensa pendente: {$link}\n"
            )
                ->append(
                    "- Plano: {$plan->name}\n",
                    "- Email: {$email}"
                )
        );
    }

    protected function sendLicensableEmail(Licensable $licensable, Plan $plan): void
    {
        $this->handleEmailTransport(function () use ($licensable, $plan) {
            $email = $licensable->getBillingEmail();
            Mail::to($email)->send(new LicensePendingMail($plan));
        });
    }
}
