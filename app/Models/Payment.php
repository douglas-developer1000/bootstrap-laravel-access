<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Libraries\Enums\PaymentTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property float $value
 * @property PaymentTypeEnum $type
 * @property int $customer_id
 * @property int $sale_id
 */
#[Fillable(['value', 'type', 'customer_id', 'sale_id'])]
final class Payment extends Model
{
    /** @use HasFactory<PaymentFactory> */
    use HasFactory;

    protected $casts = [
        'value' => 'decimal:4',
        'type' => PaymentTypeEnum::class,
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function paymentCards(): BelongsToMany
    {
        return $this->belongsToMany(PaymentCard::class)
            ->using(PaymentPaymentCard::class)
            ->withPivot(['fee_id', 'pay_way'])
            ->with(
                'paymentPaymentCard.fee',
                fn(BelongsTo $query) => $query->select(['id', 'type', 'value'])
            );
    }
}
