<?php

declare(strict_types=1);

namespace App\View\Components\Organisms;

use Override;

final class ConfirmApproveBtn extends ConfirmBtn
{
    #[Override]
    protected function getViewData(): array
    {
        return [
            ...parent::getViewData(),

            'icon' => 'bi-hand-thumbs-up',
            'btnCssClasses' => ['btn-success'],
            'method' => 'DELETE',
        ];
    }
}
