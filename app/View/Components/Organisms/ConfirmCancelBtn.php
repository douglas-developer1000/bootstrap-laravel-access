<?php

declare(strict_types=1);

namespace App\View\Components\Organisms;

use Override;

final class ConfirmCancelBtn extends ConfirmBtn
{
    #[Override]
    protected function getViewData(): array
    {
        return [
            ...parent::getViewData(),

            'icon' => 'bi-stop-circle',
            'btnCssClasses' => ['btn-danger'],
            'method' => 'PATCH',
        ];
    }
}
