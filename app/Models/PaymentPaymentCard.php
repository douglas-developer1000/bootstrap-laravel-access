<?php

declare(strict_types=1);

namespace App\Models;

use App\Libraries\Enums\CardPayWayEnum;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property CardPayWayEnum $pay_way
 * @property int $payment_id
 * @property int $payment_card_id
 * @property int $fee_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
final class PaymentPaymentCard extends Pivot
{
    protected $casts = [
        'pay_way' => CardPayWayEnum::class,
    ];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function paymentCard(): BelongsTo
    {
        return $this->belongsTo(PaymentCard::class);
    }

    public function fee(): BelongsTo
    {
        return $this->belongsTo(Discount::class, 'fee_id');
    }
}
