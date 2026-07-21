<?php

declare(strict_types=1);

namespace App\Models;

use App\Libraries\Enums\BillingPeriodEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Override;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property float $price
 * @property BillingPeriodEnum $billing_period
 * @property null|Carbon $created_at
 * @property null|Carbon $updated_at
 * @property null|Carbon $deleted_at
 * @property-read Collection<Role> $roles
 */
#[Fillable(['name', 'slug', 'description', 'price', 'billing_period'])]
final class Plan extends Model
{
    use SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    #[Override]
    protected function casts()
    {
        return [
            'billing_period' => BillingPeriodEnum::class,
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'plan_role')->withPivot('additional');
    }

    public function roleDescriptions(): HasManyThrough
    {
        return $this->hasManyThrough(RoleDescription::class, Role::class);
    }

    public function licenses(): HasMany
    {
        return $this->hasMany(License::class);
    }
}
