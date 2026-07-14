<?php

declare(strict_types=1);

namespace App\Models;

use App\Libraries\Enums\DiscountTypeEnum;
use Database\Factories\DiscountFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property DiscountTypeEnum $type
 * @property float $value
 * @property bool $native
 * @property null|Carbon $deleted_at
 * @property int $user_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[Fillable(['type', 'value', 'user_id'])]
final class Discount extends Model
{
    /** @use HasFactory<DiscountFactory> */
    use HasFactory;

    use SoftDeletes;

    protected $casts = [
        'type' => DiscountTypeEnum::class,
    ];

    public function stockEntries(): HasMany
    {
        return $this->hasMany(StockEntry::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function paymentCards()
    {
        return $this->hasManyThrough(
            PaymentCard::class,
            PaymentPaymentCard::class,
            'fee_id',
            'id',
            'id',
            'payment_id'
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
