<?php

declare(strict_types=1);

namespace App\Services\LicenseStates;

use App\Exceptions\LicenseStatusModificationException;
use App\Libraries\Enums\LicenseStatusEnum;
use App\Models\License;
use App\Services\Contracts\LicenseStatusStateInterface;

final class LicenseCanceledState implements LicenseStatusStateInterface
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
        if (!$this->license->isReactivatable) {
            throw LicenseStatusModificationException::reactivation($this->license->id);
        }
        $this->license->update(['cancelled_at' => NULL]);
        $this->license->setStatusState(
            LicenseStatusEnum::ACTIVE->parseStatusState($this->license)
        );
    }

    public function expireLicense(): void
    {
        throw LicenseStatusModificationException::expiration($this->license->id);
    }

    public function cancelLicense(): void
    {
        if (!$this->license->isPostCancellable) {
            throw LicenseStatusModificationException::cancelation($this->license->id);
        }
        $this->license->update([
            'status' => LicenseStatusEnum::CANCELED
        ]);
    }

    public function abandonLicense(): void
    {
        throw LicenseStatusModificationException::abandonment($this->license->id);
    }
}
