<?php

declare(strict_types=1);

namespace App\Services\LicenseStates;

use App\Exceptions\LicenseStatusModificationException;
use App\Libraries\Enums\InvoiceStatusEnum;
use App\Libraries\Enums\LicenseStatusEnum;
use App\Models\License;
use App\Services\Contracts\LicenseStatusStateInterface;
use Override;

final class LicenseActiveState implements LicenseStatusStateInterface
{
    public function __construct(protected License $license)
    {
        //
    }

    #[Override]
    public function changePlan(): void
    {
        if (! $this->license->allowPlanChanging) {
            throw LicenseStatusModificationException::planModification($this->license->id);
        }
        $this->license->update([
            'status' => LicenseStatusEnum::CHANGED,
            'cancelled_at' => null,
            'expires_at' => now(),
        ]);
        $this->license->setStatusState(
            LicenseStatusEnum::CHANGED->parseStatusState($this->license)
        );
    }

    #[Override]
    public function activateLicense(): void
    {
        throw LicenseStatusModificationException::activation($this->license->id);
    }

    #[Override]
    public function expireLicense(): void
    {
        if (! $this->license->isExpirable) {
            throw LicenseStatusModificationException::expiration($this->license->id);
        }
        $this->license->update([
            'status' => LicenseStatusEnum::EXPIRED,
        ]);
        $this->license->setStatusState(
            LicenseStatusEnum::EXPIRED->parseStatusState($this->license)
        );
    }

    #[Override]
    public function cancelLicense(): void
    {
        if (! $this->license->isPreCancellable) {
            throw LicenseStatusModificationException::cancelation($this->license->id);
        }
        $this->license->update([
            'cancelled_at' => now(),
        ]);
        $this->license->setStatusState(
            LicenseStatusEnum::CANCELED->parseStatusState($this->license)
        );
    }

    #[Override]
    public function abandonLicense(string $reason, InvoiceStatusEnum $invoiceStatus): void
    {
        throw LicenseStatusModificationException::abandonment($this->license->id);
    }
}
