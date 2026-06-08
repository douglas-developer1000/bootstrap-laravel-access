<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Libraries\Enums\StockExitTypeEnum;
use Database\Factories\StockExitFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['type', 'qty', 'user_id', 'stock_entry_id'])]
final class StockExit extends Model
{
    /** @use HasFactory<StockExitFactory> */
    use HasFactory;

    protected $casts = [
        'type' => StockExitTypeEnum::class
    ];

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
