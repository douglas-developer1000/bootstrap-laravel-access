<?php

declare(strict_types=1);

namespace App\Models;

use App\Libraries\Enums\StockExitTypeEnum;
use Database\Factories\StockExitFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Override;

/**
 * @property int $id
 * @property StockExitTypeEnum $type
 * @property int $qty
 * @property int $user_id
 * @property int $stock_entry_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[Fillable(['type', 'qty', 'user_id', 'stock_entry_id'])]
final class StockExit extends Model
{
    /** @use HasFactory<StockExitFactory> */
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
            'type' => StockExitTypeEnum::class,
        ];
    }

    public function sales(): BelongsToMany
    {
        return $this->belongsToMany(Sale::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function stockEntry(): BelongsTo
    {
        return $this->belongsTo(StockEntry::class);
    }
}
