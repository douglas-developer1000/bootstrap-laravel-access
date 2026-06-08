<?php

declare(strict_types=1);

namespace App\View\Components\Organisms;

use Illuminate\View\Component;
use ReflectionMethod;

class ConfirmBtn extends Component
{
    protected string $href;
    protected string $formId;

    public function __construct(
        public string $route,
        public string $heading,
        public bool $disabled = false,

        public string $positiveText = 'Sim',
        public string $negativeText = 'Manter',
        public string $title = 'Realizar ação',

        /**
         * @var array<string, string> $routeParams
         */
        public array $routeParams = [],
    ) {
        $this->href = route(
            $this->route,
            collect(
                $this->routeParams
            )->merge(request()->query->all())->all()
        );
        $this->formId = uniqid('form_');
    }

    protected function getViewData(): array
    {
        return [
            'id' => uniqid(),
            'href' => $this->href,

            'icon' => 'bi-check-lg',
            'btnDisabled' => $this->disabled,
            'btnCssClasses' => [],
            'formId' => $this->formId,
            'btnDataset' => [],
            'method' => 'POST',
        ];
    }

    public function render()
    {
        return view(
            'components.organisms.confirm-btn',
            $this->getViewData()
        );
    }
}
