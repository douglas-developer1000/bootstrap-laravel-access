<?php

declare(strict_types=1);

namespace App\Libraries\Enums;

enum LocaleEnum: string
{
    case BR = 'pt_BR';
    case US = 'en_US';

    public function getTimezone(): string
    {
        return match ($this) {
            self::BR => 'America/Sao_Paulo',
            self::US => 'America/New_York',
        };
    }
}
