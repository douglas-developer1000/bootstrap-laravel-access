<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Libraries\Enums\CardPayWayEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Database\Factories\PaymentCardFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

#[Fillable(['flag', 'pay_way', 'img', 'native', 'user_id'])]
final class PaymentCard extends Model
{
    /** @use HasFactory<PaymentCardFactory> */
    use HasFactory, SoftDeletes;

    /**
     * Parse the pay_way stored by database to a CardPayWayEnum list
     * 
     * @return Collection<CardPayWayEnum>
     */
    public function getPayWayListAttribute(): Collection
    {
        return collect(array_filter(explode('+', $this->pay_way ?? '')))->map(
            fn(string $text) => CardPayWayEnum::from($text)
        );
    }

    public function paymentPaymentCard(): HasMany
    {
        return $this->hasMany(PaymentPaymentCard::class);
    }

    public function discounts(): BelongsToMany
    {
        return $this->belongsToMany(Discount::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
