<?php

declare(strict_types=1);

namespace App\Libraries\Enums;

use App\Libraries\Traits\StorableEnumTrait;

enum CustomerPhoneTypeEnum: string
{
    use StorableEnumTrait;

    case CELULAR = 'celular';
    case COMMERCIAL = 'commercial';
    case RESIDENTIAL = 'residential';
    case OTHER = 'other';

    public function toString(): string
    {
        return match ($this) {
            self::CELULAR => 'Celular',
            self::COMMERCIAL => 'Comercial',
            self::RESIDENTIAL => 'Residencial',
            default => 'Outro'
        };
    }
}
