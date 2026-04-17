<?php

declare(strict_types=1);

namespace App\Libraries\Registration;

use App\Libraries\Registration\Contracts\HandlerInterface;
use App\Services\Contracts\RegistrationInterface;

final class RegisterOrderHandler implements HandlerInterface
{
    public function __construct(private RegistrationInterface $registrationService)
    {
        // ...
    }

    public function handle(string $email, ?string $phone): bool
    {
        $registerOrder = $this->registrationService->findRegisterOrderByEmail($email);
        if ($registerOrder) {
            $this->registrationService->updateModelPhone($registerOrder, $phone);
            return false;
        }
        return true;
    }
}
