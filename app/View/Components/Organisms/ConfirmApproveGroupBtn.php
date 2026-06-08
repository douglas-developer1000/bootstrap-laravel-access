<?php

declare(strict_types=1);

namespace App\View\Components\Organisms;

use Override;

final class ConfirmApproveGroupBtn extends ConfirmBtn
{
    #[Override]
    protected function getViewData(): array
    {
        return [
            ...parent::getViewData(),
            'btnContent' => 'Approvar selecionados',
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
                ['key' => 'name', 'value' => 'approvement[]'],
            ],
            'method' => 'DELETE'
        ];
    }
}
