<?php

declare(strict_types=1);

namespace App\View\Components\Organisms;

use Override;
use ReflectionMethod;

final class ConfirmRmGroupBtn extends ConfirmBtn
{
    #[Override]
    protected function getViewData(): array
    {
        return [
            ...parent::getViewData(),
            'btnContent' => 'Remover selecionados',
            'btnDisabled' => true,
            'btnCssClasses' => [
                'btn-secondary',
                'align-self-end',
                'justify-content-end',
                'multiselection-submit',
                'cursor-pointer'
            ],
            'btnDataset' => [
                ['key' => 'form', 'value' => $this->formId],
                ['key' => 'name', 'value' => 'remotion[]'],
            ],
            'method' => 'DELETE',
        ];
    }
}
