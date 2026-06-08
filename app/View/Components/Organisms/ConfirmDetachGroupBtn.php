<?php

declare(strict_types=1);

namespace App\View\Components\Organisms;

use Override;

final class ConfirmDetachGroupBtn extends ConfirmBtn
{
    public array $dataset;

    public function __construct(
        string $route,
        string $heading,
        bool $disabled = false,
        string $positiveText = 'Sim',
        string $negativeText = 'Manter',
        string $title = 'Realizar ação',
        array $routeParams = [],
        array $dataset = [],
    ) {
        parent::__construct(
            $route,
            $heading,
            $disabled,
            $positiveText,
            $negativeText,
            $title,
            $routeParams
        );

        $this->dataset = $dataset;
    }

    #[Override]
    protected function getViewData(): array
    {
        return [
            ...parent::getViewData(),
            'icon' => 'bi-scissors',
            'btnDisabled' => true,
            'btnCssClasses' => [
                'btn-secondary',
                'align-self-end',
                'justify-content-end',
                'multiselection-submit',
                'cursor-pointer',
                'detachment',
            ],
            'btnDataset' => [
                ['key' => 'form', 'value' => $this->formId],
                ['key' => 'name', 'value' => 'detachment[]'],
                ...$this->dataset,
            ],
            'method' => 'DELETE',
        ];
    }
}
