<?php

declare(strict_types=1);

namespace App\Services\LicenseStates;

use App\Exceptions\LicenseStatusModificationException;
use App\Libraries\Enums\InvoiceStatusEnum;
use App\Libraries\Enums\LicenseStatusEnum;
use App\Models\License;
use App\Services\Contracts\LicenseStatusStateInterface;
use Override;

final class LicenseCanceledState implements LicenseStatusStateInterface
{
    public function __construct(protected License $license)
    {
        //
    }

    #[Override]
    public function changePlan(): void
    {
        if (! $this->license->isReactivatable) {
            throw LicenseStatusModificationException::planModification($this->license->id);
        }
        $this->license->update([
            'status' => LicenseStatusEnum::CHANGED,
            'cancelled_at' => null,
        ]);
        $this->license->setStatusState(
            LicenseStatusEnum::CHANGED->parseStatusState($this->license)
        );
    }

    #[Override]
    public function activateLicense(): void
    {
        if (! $this->license->isReactivatable) {
            throw LicenseStatusModificationException::reactivation($this->license->id);
        }
        $this->license->update(['cancelled_at' => null]);
        $this->license->setStatusState(
            LicenseStatusEnum::ACTIVE->parseStatusState($this->license)
        );
    }

    #[Override]
    public function expireLicense(): void
    {
        throw LicenseStatusModificationException::expiration($this->license->id);
    }

    #[Override]
    /**
     * This method only can be called by job, not by user
     */
    public function cancelLicense(): void
    {
        if (! $this->license->isPostCancellable) {
            throw LicenseStatusModificationException::cancelation($this->license->id);
        }
        $this->license->update([
            'status' => LicenseStatusEnum::CANCELED,
        ]);
    }

    #[Override]
    public function abandonLicense(string $reason, InvoiceStatusEnum $invoiceStatus): void
    {
        throw LicenseStatusModificationException::abandonment($this->license->id);
    }
}
