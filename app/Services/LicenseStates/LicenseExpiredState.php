<?php

declare(strict_types=1);

namespace App\Services\LicenseStates;

use App\Exceptions\LicenseStatusModificationException;
use App\Libraries\Enums\InvoiceStatusEnum;
use App\Models\License;
use App\Services\Contracts\LicenseStatusStateInterface;
use Override;

final class LicenseExpiredState implements LicenseStatusStateInterface
{
    public function __construct(protected License $license)
    {
        //
    }

    #[Override]
    public function changePlan(): void
    {
        throw LicenseStatusModificationException::planModification($this->license->id);
    }

    #[Override]
    public function activateLicense(): void
    {
        throw LicenseStatusModificationException::activation($this->license->id);
    }

    #[Override]
    public function expireLicense(): void
    {
        throw LicenseStatusModificationException::expiration($this->license->id);
    }

    #[Override]
    public function cancelLicense(): void
    {
        throw LicenseStatusModificationException::cancelation($this->license->id);
    }

    #[Override]
    public function abandonLicense(string $reason, InvoiceStatusEnum $invoiceStatus): void
    {
        throw LicenseStatusModificationException::abandonment($this->license->id);
    }
}
