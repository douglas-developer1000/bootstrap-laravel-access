<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Contracts\Licensable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property int $licensable_id
 * @property string $licensable_type
 * @property float $amount
 * @property string $description
 * @property int $license_id
 * @property-read Licensable $licensable
 */
#[Fillable([
    'licensable_id',
    'licensable_type',
    'amount',
    'description',
    'license_id',
])]
final class Credit extends Model
{
    public function licensable(): MorphTo
    {
        return $this->morphTo();
    }

    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class);
    }
}
