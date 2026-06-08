<?php

declare(strict_types=1);

namespace App\Libraries\Enums;

enum PaymentTypeEnum: string
{
    case MONEY = 'money';
    case PIX = 'pix';
    case CARD = 'card';

    public function toString(): string
    {
        return match ($this) {
            self::MONEY => 'Dinheiro',
            self::PIX => 'Pix',
            self::CARD => 'Cartão',
            default => throw new \Exception("Tipo de pagamento inválido", 1)
        };
    }
}
