<?php

declare(strict_types=1);

namespace App\Libraries\Enums;

use Illuminate\Support\Number;

enum DiscountTypeEnum: string
{
    case PERCENTAGE = 'percentage';

    case RAW = 'raw';

    public function toString(): string
    {
        return match ($this) {
            self::PERCENTAGE => 'Porcentagem',
            self::RAW => 'Valor bruto',
            default => throw new \Exception("Tipo de desconto inválido", 1)
        };
    }

    public static function parseDiscountValue(string $type, float|int $value)
    {
        if (self::tryFrom($type) === self::PERCENTAGE) {
            return Number::percentage(
                number: $value,
                maxPrecision: 2,
                locale: 'pt_BR'
            );
        }
        return Number::currency(
            number: $value,
            in: 'BRL',
            locale: 'pt_BR',
            precision: 2
        );
    }
}
