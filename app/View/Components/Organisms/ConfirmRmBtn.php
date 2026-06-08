<?php

declare(strict_types=1);

namespace App\View\Components\Organisms;

use Override;
use ReflectionMethod;

/**
 * @see view('pages.customers.index')
 * @see view('pages.permissions.index')
 * @see view('pages.products.categories.index')
 * @see view('pages.register.approvals.index')
 * @see view('pages.register.orders.index')
 * @see view('pages.roles.index')
 * @see view('pages.stocks.index')
 * @see view('pages.users.index')
 */
final class ConfirmRmBtn extends ConfirmBtn
{
    #[Override]
    protected function getViewData(): array
    {
        return [
            ...parent::getViewData(),

            'icon' => 'bi-trash',
            'btnCssClasses' => ['btn-danger'],
            'method' => 'DELETE',
        ];
    }
}
