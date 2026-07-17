<?php

declare(strict_types=1);

namespace App\Models\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphOne;

interface Licensable extends HasLicenseHandling, HasRoleHandling
{
    public function getBillingEmail(): string;

    public function getMorphClass();

    public function getKey();

    public function activeLicense(): MorphOne;

    public function pendingLicense(): MorphOne;
}
