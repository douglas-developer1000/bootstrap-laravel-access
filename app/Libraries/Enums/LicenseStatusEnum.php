<?php

declare(strict_types=1);

namespace App\Libraries\Enums;

use App\Models\License;
use App\Services\Contracts\LicenseStatusStateInterface;
use App\Services\LicenseStates\LicenseAbandonedState;
use App\Services\LicenseStates\LicenseActiveState;
use App\Services\LicenseStates\LicenseCanceledState;
use App\Services\LicenseStates\LicenseChangedState;
use App\Services\LicenseStates\LicenseExpiredState;
use App\Services\LicenseStates\LicensePendingState;

enum LicenseStatusEnum: string
{
    case ACTIVE = 'active';
    case EXPIRED = 'expired';
    case CANCELED = 'canceled';
    case PENDING = 'pending';
    case CHANGED = 'changed';
    case ABANDONED = 'abandoned';

    public function toString(): string
    {
        return match ($this) {
            self::ACTIVE => 'Ativo',
            self::EXPIRED => 'Expirado',
            self::CANCELED => 'Cancelado',
            self::PENDING => 'Pendente',
            self::CHANGED => 'Modificado',
            self::ABANDONED => 'Abandonado',
        };
    }

    public function parseStatusState(License $license): LicenseStatusStateInterface
    {
        return match ($this) {
            self::ACTIVE => new LicenseActiveState($license),
            self::EXPIRED => new LicenseExpiredState($license),
            self::CANCELED => new LicenseCanceledState($license),
            self::PENDING => new LicensePendingState($license),
            self::CHANGED => new LicenseChangedState($license),
            self::ABANDONED => new LicenseAbandonedState($license),
        };
    }
}
