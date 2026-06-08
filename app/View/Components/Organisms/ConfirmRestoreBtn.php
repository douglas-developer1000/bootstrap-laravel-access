<?php

declare(strict_types=1);

namespace App\View\Components\Organisms;

use Override;

final class ConfirmRestoreBtn extends ConfirmBtn
{
    #[Override]
    protected function getViewData(): array
    {
        return [
            ...parent::getViewData(),

            'icon' => 'bi-arrow-return-left',
            'btnCssClasses' => ['btn-success'],
        ];
    }
}
