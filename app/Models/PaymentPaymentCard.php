<?php

declare(strict_types=1);

namespace App\Models;

use App\Libraries\Enums\CardPayWayEnum;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
