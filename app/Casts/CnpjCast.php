<?php

declare(strict_types=1);

namespace App\Casts;

use App\Libraries\Values\CnpjValue;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

final class CnpjCast implements CastsAttributes
{

    public function get(Model $model, string $key, mixed $value, array $attributes)
    {
        return new CnpjValue($value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes)
    {
        return [
            $key => $value->getValue()
        ];
    }
}
