<?php

declare(strict_types=1);

namespace App\Models\Contracts;

interface Licensable extends HasRoleHandling, HasLicenseHandling
{
    public function getBillingEmail(): string;

    public function getMorphClass();

    public function getKey();
}
