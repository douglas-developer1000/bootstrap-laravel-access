<?php

declare(strict_types=1);

namespace App\Libraries\Utils;

use Illuminate\Support\Facades\Validator;

final class Paginator
{
    private function __construct()
    {
        // ...
    }

    public static function buildSearch(array $params)
    {
        /** @var string|null $input **/
        $input = $params['q'] ?? NULL;

        if (!\is_string($input) || mb_strlen(trim($input), 'UTF-8') === 0) {
            return false;
        }
        return trim($input);
    }

    public static function buildGroup(array $params): int|string
    {
        /** @var int $group **/
        $group = config('pagination.group');
        /** @var int $input **/
        $input = $params['group'] ?? $group;
        $groupsParsed = implode(',', config('pagination.groups'));
        $validator = Validator::make($params, [
            'group' => "nullable|in:{$groupsParsed}"
        ]);
        if ($validator->passes()) {
            return $input;
        }
        return $group;
    }
}
