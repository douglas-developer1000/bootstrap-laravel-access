<?php

declare(strict_types=1);

namespace App\Libraries\Enums;

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
}
