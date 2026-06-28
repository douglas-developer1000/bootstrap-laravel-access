<?php

declare(strict_types=1);

namespace App\Models;

use App\Libraries\Enums\LicenseStatusEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property int $plan_id
 * @property int $licensable_id
 * @property string $licensable_type
 * @property float $price_paid
 * @property Carbon $starts_at
 * @property Carbon $expires_at
 * @property LicenseStatusEnum $status
 * @property bool $is_recurring
 * @property null|Carbon $created_at
 * @property null|Carbon $updated_at
 * @property-read Plan $plan
 */
#[Fillable([
    'plan_id',
    'licensable_id',
    'licensable_type',
    'price_paid',
    'starts_at',
    'expires_at',
    'status',
    'is_recurring',
])]
final class License extends Model
{
    protected $casts = [
        'status' => LicenseStatusEnum::class,
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_recurring' => 'bool',
    ];

    public function licensable(): MorphTo
    {
        return $this->morphTo();
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }
}
