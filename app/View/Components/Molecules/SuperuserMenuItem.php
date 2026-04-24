<?php

declare(strict_types=1);

namespace App\View\Components\Molecules;

use App\Libraries\Enums\RoleNameEnum;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Closure;

final class SuperuserMenuItem extends MenuItem
{
    public function __construct()
    {
        parent::__construct([
            'Usuários' => route('users.index'),
            'Papéis' => route('roles.index'),
            'Permissões' => route('permissions.index'),
            'Pedidos' => route('register.orders.index'),
            'Aprovações' => route('register.approvals.index')
        ]);
    }

    public function render(): View|Closure|string
    {
        /** @var User $user */
        $user = Auth::user();
        if (
            $user->email === config('app.superadmin.email') ||
            $user->hasRole(RoleNameEnum::SUPER_ADMIN->value)
        ) {
            return parent::render();
        }
        return '';
    }
}
