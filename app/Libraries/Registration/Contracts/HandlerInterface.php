<?php

declare(strict_types=1);

namespace App\Libraries\Registration\Contracts;

interface HandlerInterface
{
    public function handle(string $email, ?string $phone): bool;
}
