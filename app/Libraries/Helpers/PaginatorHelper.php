<?php

declare(strict_types=1);

namespace App\Libraries\Helpers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Uri;

final class PaginatorHelper
{
    public static function buildSearch(array $params, $keyTerm = 'q'): string|false
    {
        /** @var string|null $input * */
        $input = $params[$keyTerm] ?? null;

        if (! \is_string($input) || mb_strlen(trim($input), 'UTF-8') === 0) {
            return false;
        }

        return trim($input);
    }

    public static function buildSort(array $params, array $baseList): string
    {
        /** @var string $input * */
        $input = $params['sort'] ?? $baseList[0];

        $baseParsed = implode(',', $baseList);
        $validator = Validator::make($params, [
            'sort' => "nullable|in:{$baseParsed}",
        ]);
        if ($validator->passes()) {
            return $input;
        }

        return $baseList[0];
    }

    public static function buildOrder(array $params): string
    {
        /** @var string $input * */
        $input = $params['order'] ?? 'desc';

        $validator = Validator::make($params, [
            'order' => 'nullable|in:asc,desc',
        ]);
        if ($validator->passes()) {
            return $input;
        }

        return 'desc';
    }

    public static function buildGroup(array $params): int|string
    {
        /** @var int $group * */
        $group = config('pagination.group');
        /** @var int $input * */
        $input = $params['group'] ?? $group;
        $groupsParsed = implode(',', config('pagination.groups'));
        $validator = Validator::make($params, [
            'group' => "nullable|in:{$groupsParsed}",
        ]);
        if ($validator->passes()) {
            return $input;
        }

        return $group;
    }

    public static function makeHref(string $url, Collection $currentQueryString, $group = null): string
    {
        $uri = Uri::of($url);
        $groupSelected = self::buildGroup(['group' => $currentQueryString->get('group')]);
        $qs = $currentQueryString->merge($uri->query());

        if ($group !== null) {
            $qs = [
                ...$qs->except(['group'])->all(),
                'group' => $group,
            ];

            return $uri->withQuery($qs)->toString();
        }
        if ($qs->has('group')) {
            $qs->put('group', $groupSelected);
        }

        return $uri->withQuery($qs->all())->toString();
    }
}
