<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Libraries\Enums\DiscountTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Database\Factories\DiscountFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property DiscountTypeEnum $type
 * @property float $value
 * @property bool $native
 * @property null|\Illuminate\Support\Carbon $deleted_at
 * @property int $user_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
#[Fillable(['type', 'value', 'user_id'])]
final class Discount extends Model
{
    use SoftDeletes;

    /** @use HasFactory<DiscountFactory> */
    use HasFactory;

    protected $casts = [
        'type' => DiscountTypeEnum::class
    ];

    public function stockEntries(): HasMany
    {
        return $this->hasMany(StockEntry::class);
    }

    public function stockExits(): HasMany
    {
        return $this->hasMany(StockExit::class);
    }

    public function paymentCards()
    {
        return $this->belongsToMany(PaymentCard::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
