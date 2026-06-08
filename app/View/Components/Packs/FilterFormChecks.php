<?php

declare(strict_types=1);

namespace App\View\Components\Packs;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\Component;
use ReflectionMethod;

final class FilterFormChecks extends Component
{
    /**
     * @var array<string, string> $checkboxes
     */
    public array $checkboxes;
    protected Request $request;
    protected Collection $qs;
    public function __construct(array $checkboxes)
    {
        $this->checkboxes = $checkboxes;
        $this->request = request();
        $this->qs = collect($this->request->query());
    }

    public function render()
    {
        return view('components.packs.filter-form-checks', [
            'checkedStatus' => fn(string $key) => (
                !$this->qs->has($key) || $this->request->boolean($key)
            )
        ]);
    }
}
