<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\StockEntryFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['cost', 'qty', 'validity', 'product_id', 'supplier_id', 'user_id', 'discount_id'])]
final class StockEntry extends Model
{
    /** @use HasFactory<StockEntryFactory> */
    use HasFactory;

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class);
    }
}
