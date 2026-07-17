<?php

declare(strict_types=1);

namespace App\Services\LicenseStates;

use App\Exceptions\LicenseStatusModificationException;
use App\Libraries\Enums\InvoiceStatusEnum;
use App\Libraries\Enums\LicenseStatusEnum;
use App\Models\License;
use App\Services\Contracts\LicenseStatusStateInterface;
use Illuminate\Support\Facades\DB;
use Override;

final class LicensePendingState implements LicenseStatusStateInterface
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
        if (! $this->license->isActivatable) {
            throw LicenseStatusModificationException::activation($this->license->id);
        }
        $this->license->update([
            'status' => LicenseStatusEnum::ACTIVE,
        ]);
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
    public function cancelLicense(): void
    {
        throw LicenseStatusModificationException::cancelation($this->license->id);
    }

    #[Override]
    public function abandonLicense(string $reason, InvoiceStatusEnum $invoiceStatus): void
    {
        DB::transaction(function () use ($reason, $invoiceStatus) {
            if (! $this->license->isAbandonable) {
                throw LicenseStatusModificationException::abandonment($this->license->id);
            }
            $this->license->annullCredit($reason);
            $this->license->annulInvoices($invoiceStatus);

            $this->license->update([
                'status' => LicenseStatusEnum::ABANDONED,
            ]);
            $this->license->setStatusState(
                LicenseStatusEnum::ABANDONED->parseStatusState($this->license)
            );
        });
    }
}
