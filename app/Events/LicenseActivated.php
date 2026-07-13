<?php

declare(strict_types=1);

namespace App\Events;

use App\Mail\LicenseActiveMail;
use App\Models\Contracts\Licensable;
use App\Models\License;
use App\Models\Plan;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

final class LicenseActivated
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
        $this->notifySlack($licensable, $plan, $license);
        $this->sendLicensableEmail($licensable, $license);
    }

    protected function notifySlack(Licensable $licensable, Plan $plan, License $newLicense): void
    {
        $link = route('licenses.show', ['license' => $newLicense->id], true);
        $email = $licensable->getBillingEmail();

        Log::channel('slack')->info(
            Str::of(
                "Licensa ativada: {$link}\n"
            )
                ->append(
                    "- Plano: {$plan->name}\n",
                    "- Email: {$email}"
                )
        );
    }

    protected function sendLicensableEmail(Licensable $licensable, License $newLicense): void
    {
        Mail::to(
            $licensable->getBillingEmail()
        )->send(new LicenseActiveMail($newLicense));
    }
}
