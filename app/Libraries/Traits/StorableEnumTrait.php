<?php

declare(strict_types=1);

namespace App\Libraries\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use \BackedEnum;

trait StorableEnumTrait
{
    public static function combineEnumValues(): array
    {
        $itens = array_column(self::cases(), 'value');

        $resultado = [];

        $n = count($itens);

        // travel each possible subset (except empty)
        for ($i = 1; $i < (1 << $n); $i++) {
            $subset = [];
            for ($j = 0; $j < $n; $j++) {
                if ($i & (1 << $j)) {
                    $subset[] = $itens[$j];
                }
            }
            $resultado[] = implode('+', $subset);
        }

        return $resultado;
    }

    public static function defineRequestBooleanEnumKeys($key): array
    {
        return collect(
            array_column(self::cases(), 'value')
        )->map(fn($value) => "{$key}.{$value}")->all();
    }

    public static function wrapRequestBooleanEnum(Request $request, $key): string|false
    {
        $output = implode('+', array_filter(
            collect(self::defineRequestBooleanEnumKeys($key))->map(
                function (string $requestKey) use (&$request, $key) {
                    if (!$request->boolean($requestKey)) {
                        return false;
                    }
                    return Str::of($requestKey)->after("{$key}.")->toString();
                }
            )->all()
        ));
        if (!$output) {
            return false;
        }
        return $output;
    }

    public static function casesExcept(BackedEnum ...$enumList)
    {
        $enumCollection = collect($enumList);
        return collect(self::cases())->filter(
            fn($item) => !$enumCollection->contains(fn($neddle) => $neddle === $item)
        )->all();
    }
}
