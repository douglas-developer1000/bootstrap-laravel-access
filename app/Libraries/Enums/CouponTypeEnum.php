<?php

declare(strict_types=1);

namespace App\Libraries\Enums;

use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Illuminate\Support\Number;

enum CouponTypeEnum: string
{
    case PERCENTAGE = 'percentage';
    case FIXED = 'fixed';

    public function defineDiscount(BigDecimal $price, BigDecimal $discount): BigDecimal
    {
        return match ($this) {
            self::PERCENTAGE => $price->multipliedBy(
                $discount->dividedBy(100, 3, RoundingMode::Floor)
            ),
            self::FIXED => $discount,
        };
    }

    public function parseViewValue(?BigDecimal $discount): ?string
    {
        return match ($this) {
            self::PERCENTAGE => Number::percentage(
                number: $discount?->toFloat() ?? 0,
                maxPrecision: 2,
                locale: 'pt_BR'
            ),
            default => Number::currency(
                number: $discount?->toFloat() ?? 0,
                in: 'BRL',
                locale: 'pt_BR',
                precision: 2
            )
        };
    }
}
