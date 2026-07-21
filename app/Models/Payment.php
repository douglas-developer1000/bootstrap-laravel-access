<?php

declare(strict_types=1);

namespace App\Models;

use App\Libraries\Enums\PaymentTypeEnum;
use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Override;

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

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    #[Override]
    protected function casts()
    {
        return [
            'value' => 'decimal:4',
            'type' => PaymentTypeEnum::class,
        ];
    }

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
                fn (BelongsTo $query) => $query->select(['id', 'type', 'value'])
            );
    }
}
