<?php

declare(strict_types=1);

namespace App\Libraries\Registration\Contracts;

use App\Libraries\Values\PhoneValue;

interface HandlerInterface
{
    public function handle(string $email, PhoneValue $phone): bool;
}
