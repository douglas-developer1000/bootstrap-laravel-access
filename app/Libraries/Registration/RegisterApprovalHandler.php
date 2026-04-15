<?php

declare(strict_types=1);

namespace App\Libraries\Registration;

use App\Libraries\Registration\Contracts\HandlerInterface;
use App\Libraries\Utils\TokenBuilder;
use App\Notifications\RegisterApprovalNotification;
use App\Services\Contracts\RegistrationServiceInterface;
use Carbon\Carbon;

class RegisterApprovalHandler implements HandlerInterface
{
    public function __construct(protected RegistrationServiceInterface $registrationService)
    {
        // ...
    }

    public function handle(string $email, ?string $phone): bool
    {
        $approval = $this->registrationService->findRegisterApprovalByEmail($email);
        if ($approval) {
            if (Carbon::now()->greaterThan(Carbon::parse($approval->expiration_data))) {
                $approval->token = TokenBuilder::build();
                $this->registrationService->updateRegisterApproval(
                    id: $approval->id,
                    token: $approval->token,
                    expirationData: Carbon::now()->addHours(
                        \intval(config('registration.timeout.token'))
                    )
                );
            }
            $this->registrationService->updateModelPhone($approval, $phone);
            $approval->notify(new RegisterApprovalNotification);
            return false;
        }
        return true;
    }
}
