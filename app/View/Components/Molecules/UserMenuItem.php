<?php

declare(strict_types=1);

namespace App\View\Components\Molecules;

use App\Libraries\Enums\PermissionNameEnum;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Closure;

final class UserMenuItem extends MenuItem
{
    public function __construct()
    {
        parent::__construct([
            'Clientes' => route('customers.index'),
        ]);
    }

    public function render(): View|Closure|string
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user->can(PermissionNameEnum::CUSTOMER_INDEX->value)) {
            return '';
        }
        return parent::render();
    }
}
