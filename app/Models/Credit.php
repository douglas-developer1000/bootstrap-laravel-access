<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Attributes\Fillable;

/**
 * @property int $id
 * @property int $licensable_id
 * @property string $licensable_type
 * @property float $amount
 * @property string $description
 */
#[Fillable([
    'licensable_id',
    'licensable_type',
    'amount',
    'description'
])]
final class Credit extends Model
{
    public function licensable(): MorphTo
    {
        return $this->morphTo();
    }
}
