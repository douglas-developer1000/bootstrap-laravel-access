<?php

declare(strict_types=1);

namespace App\View\Components\Organisms;

use Override;

final class ConfirmAttachBtn extends ConfirmBtn
{
    #[Override]
    protected function getViewData(): array
    {
        return [
            ...parent::getViewData(),

            'icon' => 'bi-paperclip',
            'btnCssClasses' => ['btn-primary'],
        ];
    }
}
