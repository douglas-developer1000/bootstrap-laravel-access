<?php

declare(strict_types=1);

namespace App\Libraries\Enums;

use App\Libraries\Traits\StorableEnumTrait;

enum DayPeriodsEnum: string
{
    use StorableEnumTrait;

    case MORNING = 'morning';
    case AFTERNOON = 'afternoon';
    case NIGHT = 'night';

    public function toString(): string
    {
        return match ($this) {
            self::MORNING => 'Manhã',
            self::AFTERNOON => 'Tarde',
            self::NIGHT => 'Noite',
            default => throw new \Exception("Período do dia inválido", 1)
        };
    }
}
