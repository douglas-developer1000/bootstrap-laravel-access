<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\BigDecimalCast;
use App\Libraries\Enums\CouponTypeEnum;
use Brick\Math\BigDecimal;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Override;

/**
 * @property string $code O código que o usuário digita
 * @property string $name O nome interno do cupom
 * @property CouponTypeEnum $type
 * @property BigDecimal $discount Ex: 10.00 para R$10 ou 20.00 para 20%
 */
#[Fillable(['code', 'name', 'type', 'discount'])]
final class Coupon extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    #[Override]
    protected function casts()
    {
        return [
            'type' => CouponTypeEnum::class,
            'discount' => BigDecimalCast::class,
        ];
    }
}
