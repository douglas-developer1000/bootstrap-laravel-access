<?php

namespace App\View\Components\Molecules;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\UrlWindow;
use Illuminate\Support\Uri;

class RootPagination extends Component
{
    /** @var Collection<string, string> */
    protected Collection $qs;

    /** ---------------------------------------------
     * Properties below are accessed by blade view
     * ----------------------------------------------
     */

    public int $groupChosen;

    /** @var array<string, string> $qsNoGroup */
    public array $qsNoGroup = [];

    public array $elements;

    /** -------------------------------------------------------------
     * Properties below are passed by blade view component's client
     * --------------------------------------------------------------
     */

    public LengthAwarePaginator $paginator;

    /** @var 'top'|'bottom' $spacing */
    public $spacing;

    /**
     * Create a new component instance.
     */
    public function __construct(
        LengthAwarePaginator $paginator,
        $spacing = 'top'
    ) {
        $this->qs = collect(request()->query());
        $this->groupChosen = $this->qs->get(
            'group',
            config('pagination.group')
        );
        $this->qsNoGroup = $this->qs->except(['group'])->all();

        $this->paginator = $paginator;
        $this->spacing = $spacing;
        $this->elements = $this->buildElements($paginator);
    }

    /**
     * Get the array of elements to pass to the view.
     * 
     * @see Illuminate\Pagination\LengthAwarePaginator::elements()
     *
     * @return array
     */
    protected function buildElements(LengthAwarePaginator $paginator)
    {
        $window = UrlWindow::make($paginator);

        return array_filter([
            $window['first'],
            is_array($window['slider']) ? '...' : null,
            $window['slider'],
            is_array($window['last']) ? '...' : null,
            $window['last'],
        ]);
    }

    public function makeHref(string $url, int|string $group, array $qsRemain = [])
    {
        return Uri::of($url)->withQuery(['group' => $group, ...$qsRemain])->toString();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.molecules.root-pagination');
    }
}
