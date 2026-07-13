<?php

declare(strict_types=1);

namespace App\Services\LicenseStates;

use App\Exceptions\LicenseStatusModificationException;
use App\Libraries\Enums\LicenseStatusEnum;
use App\Models\License;
use App\Services\Contracts\LicenseStatusStateInterface;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final class LicensePendingState implements LicenseStatusStateInterface
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
        if (!$this->license->isActivatable) {
            throw LicenseStatusModificationException::activation($this->license->id);
        }
        $this->license->update([
            'status' => LicenseStatusEnum::ACTIVE
        ]);
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
        throw LicenseStatusModificationException::cancelation($this->license->id);
    }

    public function abandonLicense(string $reason): void
    {
        if (!$this->license->isAbandonable) {
            throw LicenseStatusModificationException::abandonment($this->license->id);
        }
        $this->annullCredit($reason);
        $this->license->update([
            'status' => LicenseStatusEnum::ABANDONED
        ]);
        $this->license->setStatusState(
            LicenseStatusEnum::ABANDONED->parseStatusState($this->license)
        );
    }

    protected function annullCredit(string $reason): void
    {
        DB::transaction(function () use ($reason) {
            $associatedCredits = $this->license->credits;

            foreach ($associatedCredits as $credit) {
                $this->license->credits()->create([
                    'licensable_type' => $this->license->licensable_type,
                    'licensable_id'   => $this->license->licensable_id,
                    'amount'          => -$credit->amount,
                    'description'     => "Estorno de crédito automático: {$reason}"
                ]);
            }
        });
    }
}
