<?php

namespace App\View\Components\Molecules;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\UrlWindow;
use Illuminate\Support\Uri;
use App\Libraries\Utils\Paginator as PaginatorBuilder;
use Closure;

class RootPagination extends Component
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

    /** @var 'top'|'bottom' $spacing */
    public $spacing;

    /**
     * @var int|string $groupSelected
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
        $this->groupSelected = $this->pickValidGroup($this->qs->get('group'));
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

    /**
     * Verify if '$group' is into 'groups' array values
     */
    protected function pickValidGroup(int|string|null $group = NULL)
    {
        return PaginatorBuilder::buildGroup(['group' => $group]);
    }

    public function makeHref(string $url, $group = NULL)
    {
        $uri = Uri::of($url);
        $qs = $this->qs->merge($uri->query());

        if ($group !== NULL) {
            $qs = [
                ...$qs->except(['group'])->all(),
                'group' => $group
            ];
            return $uri->withQuery($qs)->toString();
        }
        if ($qs->has('group')) {
            $qs->put('group', $this->groupSelected);
        }
        return $uri->withQuery($qs->all())->toString();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.molecules.root-pagination');
    }
}
