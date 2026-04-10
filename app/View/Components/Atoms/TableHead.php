<?php

namespace App\View\Components\Atoms;

use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use App\Libraries\Utils\Paginator as PaginatorBuilder;
use Closure;

class TableHead extends Component
{
    /** @var Collection<string, string> */
    protected Collection $qs;

    public string $sort;

    public bool $default;

    /**
     * Create a new component instance.
     */
    public function __construct(string $sort, bool $default = false)
    {
        $this->qs = collect(request()->query());
        $this->sort = $sort;
        $this->default = $default;
    }

    public function makeHref(string $url, $group = NULL)
    {
        $qs = $this->qs->merge([
            // putting ordering query string
            'order' => $this->defineOrder(),
            // putting sorting query string
            'sort' => $this->sort
        ]);

        return PaginatorBuilder::makeHref($url, $qs, $group);
    }

    protected function defineOrder()
    {
        $previousSort = $this->qs->get('sort');
        if ($previousSort === NULL) {
            return $this->default ? 'asc' : 'desc';
        }
        if ($this->sort === $previousSort) {
            return $this->qs->get('order') === 'desc' ? 'asc' : 'desc';
        }
        return 'desc';
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.atoms.table-head');
    }
}
