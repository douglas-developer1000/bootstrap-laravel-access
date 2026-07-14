<?php

declare(strict_types=1);

namespace App\Libraries\Traits;

use App\Libraries\Enums\RoleNameEnum;
use App\Models\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @method static Model firstOrCreate(array $attributes = [], array $values = [])
 */
trait HandlerAnonymousTrait
{
    protected static function getSuperAdmins(): BelongsToMany
    {
        return Role::whereName(
            RoleNameEnum::SUPER_ADMIN->value
        )->first()->users();
    }

    /**
     * NOTE: If there is a language requirement, this method must search the "anonymous name"
     * by user locale
     *
     * @return string The anonymous name used by automatic and hidden tagging context
     */
    public static function getAnonymousName(): string
    {
        return 'Anônimo';
    }

    public static function getAnonymous(): self
    {
        return static::firstOrCreate([
            'native' => true,
            'name' => self::getAnonymousName(),
            'user_id' => self::getSuperAdmins()->first(['id'])->id,
        ]);
    }

    public function scopeNotAnonymous(Builder $query): Builder
    {
        return $query->whereNot('name', self::getAnonymousName());
    }
}
