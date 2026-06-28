<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Role as SpatieRole;

final class Role extends SpatieRole
{
    public function plans(): BelongsToMany
    {
        return $this->belongsToMany(Plan::class, 'plan_role');
    }
}
