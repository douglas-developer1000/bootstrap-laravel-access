<?php

declare(strict_types=1);

namespace App\View\Components\Molecules;

use App\Facades\Paginator;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\UrlWindow;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

final class RootPagination extends Component
{
    /** @var Collection<string, string> */
    protected Collection $qs;

    /** ---------------------------------------------
     * Properties below are accessed by blade view
     * ----------------------------------------------
     */
    public array $elements;

    /** -------------------------------------------------------------
     * Properties below are passed by blade view component's client
     * --------------------------------------------------------------
     */
    public LengthAwarePaginator $paginator;

    /** @var 'top'|'bottom' */
    public $spacing;

    /**
     * @var int|string
     */
    public $groupSelected;

    /**
     * Create a new component instance.
     */
    public function __construct(
        LengthAwarePaginator $paginator,
        $spacing = 'top'
    ) {
        $this->qs = collect(request()->query());

        $this->elements = $this->buildElements($paginator);
        $this->paginator = $paginator;
        $this->spacing = $spacing;
        $this->groupSelected = Paginator::buildGroup([
            'group' => $this->qs->get('group'),
        ]);
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
            \is_array($window['slider']) ? '...' : null,
            $window['slider'],
            \is_array($window['last']) ? '...' : null,
            $window['last'],
        ]);
    }

    public function makeHref(string $url, $group = null)
    {
        return Paginator::makeHref($url, $this->qs, $group);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.molecules.root-pagination');
    }
}
