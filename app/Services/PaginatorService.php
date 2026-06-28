<?php

declare(strict_types=1);

namespace App\Services;

use Closure;
use Illuminate\Container\Container;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Uri;

final class PaginatorService
{
    public function buildSearch(array $params, $keyTerm = 'q'): string|false
    {
        /** @var string|null $input * */
        $input = $params[$keyTerm] ?? null;

        if (! \is_string($input) || mb_strlen(trim($input), 'UTF-8') === 0) {
            return false;
        }

        return trim($input);
    }

    public function buildSort(array $params, array $baseList)
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

    public function buildOrder(array $params)
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

    public function buildGroup(array $params): int|string
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

    public function makeHref(string $url, Collection $currentQueryString, $group = null)
    {
        $uri = Uri::of($url);
        $groupSelected = $this->buildGroup(['group' => $currentQueryString->get('group')]);
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

    public function orderByInnerSort(LengthAwarePaginator $paginator, Closure $callback): LengthAwarePaginator
    {
        $collection = collect($paginator->items())->sortBy($callback);

        return Container::getInstance()->makeWith(LengthAwarePaginator::class, [
            'items' => $collection,
            'total' => $collection->count(),
            'perPage' => $paginator->perPage(),
            'currentPage' => $paginator->currentPage(),
            'options' => [
                'path' => $paginator->url(0),
                'pageName' => $paginator->getPageName(),
                'query' => $paginator->getOptions()['query'] ?? [],
                'fragment' => $paginator->getOptions()['fragment'] ?? null,
            ],
        ]);
    }
}
