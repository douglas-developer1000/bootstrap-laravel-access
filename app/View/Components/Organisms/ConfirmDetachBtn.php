<?php

declare(strict_types=1);

namespace App\View\Components\Organisms;

use Override;

final class ConfirmDetachBtn extends ConfirmBtn
{
    #[Override]
    protected function getViewData(): array
    {
        return [
            ...parent::getViewData(),

            'icon' => 'bi-x-circle',
            'btnCssClasses' => ['btn-primary'],
            'method' => 'DELETE',
        ];
    }
}
