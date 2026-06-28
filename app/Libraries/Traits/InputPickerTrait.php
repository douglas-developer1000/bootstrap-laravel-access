<?php

declare(strict_types=1);

namespace App\Libraries\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

trait InputPickerTrait
{
    /**
     * @param  string|array<string, string>  $keys
     */
    public function pickInputs(Request $request, array $base, string|array ...$keys): array
    {
        return collect($keys)->reduce(
            function (Collection $base, string|array $next) use (&$request) {
                if (\is_string($next)) {
                    $input = $request->input($next);
                    if ($input !== null) {
                        $base->put($next, $input);
                    }
                } else {
                    $requestKey = array_key_first($next);
                    $input = $request->input($requestKey);
                    if ($input !== null) {
                        $modelKey = $next[$requestKey];
                        $base->put($modelKey, $input);
                    }
                }

                return $base;
            },
            collect($base)
        )->all();
    }
}
