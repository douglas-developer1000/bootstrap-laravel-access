<?php

declare(strict_types=1);

namespace App\Observers;

use App\Libraries\Enums\LicenseStatusEnum;
use App\Models\Contracts\HasRoleHandling;
use App\Models\License;

final class LicenseObserver
{
    protected function isRoleRemotion(License $license): bool
    {
        if (! in_array($license->status, [LicenseStatusEnum::CANCELED, LicenseStatusEnum::EXPIRED])) {
            return false;
        }
        if ($license->getOriginal('status') !== LicenseStatusEnum::ACTIVE) {
            return false;
        }

        return true;
    }

    protected function isRoleAddition(License $license): bool
    {
        return $license->status === LicenseStatusEnum::ACTIVE;
    }

    protected function handleRoleRemotion(License $license): void
    {
        $owner = $license->licensable;
        if ($owner instanceof HasRoleHandling) {
            $owner->syncRoles([]);
        }
    }

    protected function handleRoleAddition(License $license): void
    {
        $owner = $license->licensable;
        if ($owner instanceof HasRoleHandling) {
            $owner->syncRoles(...$license->plan->roles);
        }
    }

    /**
     * Handle the License "updated" event.
     */
    public function updated(License $license): void
    {
        if (! $license->isDirty('status')) {
            return;
        }
        if ($this->isRoleRemotion($license)) {
            $this->handleRoleRemotion($license);
        } elseif ($this->isRoleAddition($license)) {
            $this->handleRoleAddition($license);
        }
    }
}
