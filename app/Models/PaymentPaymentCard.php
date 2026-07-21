<?php

declare(strict_types=1);

namespace App\Models;

use App\Libraries\Enums\CardPayWayEnum;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;
use Override;

/**
 * @property int $id
 * @property CardPayWayEnum $pay_way
 * @property int $payment_id
 * @property int $payment_card_id
 * @property int $fee_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
final class PaymentPaymentCard extends Pivot
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
            'pay_way' => CardPayWayEnum::class,
        ];
    }

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
