<?php

declare(strict_types=1);

namespace App\View\Components\Organisms;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\Component;
use ReflectionMethod;

final class FilterFormCheck extends Component
{
    protected Request $request;
    protected Collection $qs;

    public function __construct(
        public bool $checked = false,
        public ?string $key = NULL,
    ) {
        if ($this->key === NULL) {
            $this->key = uniqid('key_');
        }

        $this->request = request();
        $this->qs = collect($this->request->query())->except($key);
    }
    public function render()
    {
        return view('components.organisms.filter-form-check', [
            'action' => $this->request->fullUrlWithoutQuery($this->key),
            'qs' => $this->qs,
            'nextValue' => \intval(!$this->checked)
        ]);
    }
}
