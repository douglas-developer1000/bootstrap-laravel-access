<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Models\Role as SpatieRole;

/**
 * @property int $id
 * @property string $name
 * @property string $summary
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
final class Role extends SpatieRole
{
    public function plans(): BelongsToMany
    {
        return $this->belongsToMany(Plan::class, 'plan_role');
    }

    public function roleDescriptions(): HasMany
    {
        return $this->hasMany(RoleDescription::class);
    }
}
