<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\CnpjCast;
use Database\Factories\SupplierFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Override;

/**
 * @property int $id
 * @property string $name
 * @property null|string $cnpj
 * @property null|string $color
 * @property null|string $obs
 * @property bool $native
 * @property int $user_id
 * @property null|Carbon $deleted_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[Fillable(['name', 'cnpj', 'img', 'color', 'obs', 'native', 'user_id'])]
final class Supplier extends Model
{
    /** @use HasFactory<SupplierFactory> */
    use HasFactory, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    #[Override]
    protected function casts()
    {
        return [
            'cnpj' => CnpjCast::class,
        ];
    }

    public function stockEntries(): HasMany
    {
        return $this->hasMany(StockEntry::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function getAnonymousSupplier(): self
    {
        return self::firstOrCreate([
            'native' => true,
            'name' => 'anonymous',
            'user_id' => User::getSuperAdmins()->first(['id'])->id,
        ]);
    }
}
