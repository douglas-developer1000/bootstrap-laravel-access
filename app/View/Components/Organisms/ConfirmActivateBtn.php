<?php

declare(strict_types=1);

namespace App\View\Components\Organisms;

use Override;

final class ConfirmActivateBtn extends ConfirmApproveBtn
{
    #[Override]
    protected function getViewData(): array
    {
        return [
            ...parent::getViewData(),
            'method' => 'PATCH',
        ];
    }
}
