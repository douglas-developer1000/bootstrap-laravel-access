<?php

declare(strict_types=1);

namespace App\Models;

use App\Libraries\Traits\HandlerAnonymousTrait;
use Database\Factories\ProductCategoryFactory;
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
 * @property int $parent_id
 * @property int $user_id
 * @property bool $native
 * @property null|Carbon $deleted_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[Fillable(['name', 'parent_id', 'user_id', 'native'])]
final class ProductCategory extends Model
{
    /** @use HasFactory<ProductCategoryFactory> */
    use HandlerAnonymousTrait, HasFactory, SoftDeletes;

    #[Override]
    protected function casts(): array
    {
        return [
            'native' => 'boolean',
        ];
    }

    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }

    public function isDescendant(ProductCategory $category): bool
    {
        $parent = $this->parent;
        while ($parent != null) {
            if ($parent->id == $category->id) {
                return true;
            }
            $parent = $parent->parent;
        }

        return false;
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
