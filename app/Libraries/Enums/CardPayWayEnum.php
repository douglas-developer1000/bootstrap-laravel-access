<?php

declare(strict_types=1);

namespace App\Libraries\Enums;

use App\Libraries\Traits\StorableEnumTrait;

enum CardPayWayEnum: string
{
    use StorableEnumTrait;
    case DEBIT = 'debit';
    case CREDIT = 'credit';

    public function toString(): string
    {
        return match ($this) {
            self::CREDIT => 'Crédito',
            self::DEBIT => 'Débito',
            default => throw new \Exception("Tipo de pagamento inválido", 1)
        };
    }
}
