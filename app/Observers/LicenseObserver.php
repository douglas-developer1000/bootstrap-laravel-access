<?php

declare(strict_types=1);

namespace App\Observers;

use App\Libraries\Enums\LicenseStatusEnum;
use App\Models\Contracts\HasRoleHandling;
use App\Models\License;

final class LicenseObserver
{
    /**
     * Define if the license is the result of a activity stoppage
     */
    protected function needRoleRemotion(License $license): bool
    {
        if (! in_array($license->status, [LicenseStatusEnum::CANCELED, LicenseStatusEnum::EXPIRED, LicenseStatusEnum::CHANGED])) {
            return false;
        }
        if ($license->getOriginal('status') !== LicenseStatusEnum::ACTIVE) {
            return false;
        }

        return true;
    }

    /**
     * Handle the License "updated" event.
     */
    public function updated(License $license): void
    {
        if (! $license->isDirty('status') || ! $this->needRoleRemotion($license)) {
            return;
        }
        $owner = $license->licensable;
        if ($owner instanceof HasRoleHandling) {
            $owner->removeRole(
                $license->pullBoundRoleNames()
            );
        }
    }
}
