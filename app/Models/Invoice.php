<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\BigDecimalCast;
use App\Libraries\Enums\GatewayTypeEnum;
use App\Libraries\Enums\InvoicePaymentTypeEnum;
use App\Libraries\Enums\InvoiceStatusEnum;
use App\Models\Contracts\Licensable;
use Brick\Math\BigDecimal;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Override;

/**
 * @property int $id
 * @property int $license_id
 * @property string $licensable_type
 * @property int $licensable_id
 * @property BigDecimal $amount
 * @property GatewayTypeEnum $gateway
 * @property string|null $gateway_transaction_id
 * @property InvoiceStatusEnum $status
 * @property InvoicePaymentTypeEnum $payment_method
 * @property array $payment_details
 * @property null|Carbon $created_at
 * @property null|Carbon $updated_at
 * @property null|Carbon $voided_at
 * @property null|Carbon $failed_at
 * @property null|Carbon $expired_at
 * @property-read Licensable $licensable
 * @property-read License $license
 */
#[Fillable([
    'license_id',
    'licensable_type',
    'licensable_id',
    'amount',
    'gateway',
    'gateway_transaction_id',
    'status',
    'payment_method',
    'payment_details',
    'voided_at',
    'failed_at',
    'expired_at',
])]
final class Invoice extends Model
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
            'amount' => BigDecimalCast::class,
            'gateway' => GatewayTypeEnum::class,
            'status' => InvoiceStatusEnum::class,
            'payment_method' => InvoicePaymentTypeEnum::class,
            'payment_details' => 'array',
        ];
    }

    public function licensable(): MorphTo
    {
        return $this->morphTo();
    }

    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class);
    }
}
