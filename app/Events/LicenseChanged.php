<?php

declare(strict_types=1);

namespace App\Events;

use App\Libraries\Traits\EmailTranspHandlerTrait;
use App\Mail\PlanSwitchMail;
use App\Models\Contracts\Licensable;
use App\Models\License;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

final class LicenseChanged
{
    use Dispatchable, EmailTranspHandlerTrait, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly Licensable $licensable,
        public readonly License $newLicense,
        public readonly ?License $oldLicense
    ) {
        $this->notifySlack($licensable, $newLicense, $oldLicense);
        $this->sendLicensableEmail($licensable, $newLicense);
    }

    protected function notifySlack(Licensable $licensable, License $newLicense, ?License $oldLicense): void
    {
        $oldLink = route('licenses.show', ['license' => $oldLicense->id], true);
        $newLink = route('licenses.show', ['license' => $newLicense->id], true);
        $email = $licensable->getBillingEmail();

        Log::channel('slack')->info(
            Str::of(
                "Interrupção de Licensa por troca: {$oldLink}\n"
            )
                ->append(
                    "- Plano: {$oldLicense->plan->name}\n",
                    "- Email: {$email}"
                )
        );
        Log::channel('slack')->info(
            Str::of(
                "Mudança de Licensa: {$newLink}\n"
            )
                ->append(
                    "- Plano: {$newLicense->plan->name}\n",
                    "- Email: {$email}"
                )
        );
    }

    protected function sendLicensableEmail(Licensable $licensable, License $newLicense): void
    {
        $this->handleEmailTransport(function () use ($licensable, $newLicense) {
            Mail::to(
                $licensable->getBillingEmail()
            )->send(new PlanSwitchMail($newLicense));
        });
    }
}
