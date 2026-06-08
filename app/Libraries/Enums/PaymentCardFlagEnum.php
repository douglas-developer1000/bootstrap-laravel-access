<?php

declare(strict_types=1);

namespace App\Libraries\Enums;

enum PaymentCardFlagEnum: string
{
    case VISA = 'visa';
    case MASTER = 'master';
    case ELO =  'elo';
    case AMERICAN_EXPRESS = 'american-express';
    case HIPERCARD = 'hipercard';

    public function toString(): string
    {
        return match ($this) {
            self::VISA => 'Visa',
            self::MASTER => 'Mastercard',
            self::ELO => 'Elo',
            self::AMERICAN_EXPRESS => 'American Express',
            self::HIPERCARD => 'Hipercard',
            default => throw new \Exception("Bandeira inválida", 1)
        };
    }
}
