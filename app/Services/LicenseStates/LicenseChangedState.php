<?php

declare(strict_types=1);

namespace App\Services\LicenseStates;

use App\Exceptions\LicenseStatusModificationException;
use App\Models\License;
use App\Services\Contracts\LicenseStatusStateInterface;

final class LicenseChangedState implements LicenseStatusStateInterface
{
    public function __construct(protected License $license)
    {
        // 
    }
    public function changePlan(): void
    {
        throw LicenseStatusModificationException::planModification($this->license->id);
    }

    public function activateLicense(): void
    {
        throw LicenseStatusModificationException::activation($this->license->id);
    }

    public function expireLicense(): void
    {
        throw LicenseStatusModificationException::expiration($this->license->id);
    }

    public function cancelLicense(): void
    {
        throw LicenseStatusModificationException::cancelation($this->license->id);
    }

    public function abandonLicense(): void
    {
        throw LicenseStatusModificationException::abandonment($this->license->id);
    }
}
