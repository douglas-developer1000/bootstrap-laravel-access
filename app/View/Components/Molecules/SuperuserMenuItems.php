<?php

declare(strict_types=1);

namespace App\View\Components\Molecules;

use App\Libraries\Enums\RoleNameEnum;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Override;

final class SuperuserMenuItems extends MenuItem
{
    public function __construct()
    {
        parent::__construct([
            'Usuários' => route('users.index'),
            'Licenças' => route('licenses.index'),
            'Planos' => route('plans.index'),
            'Papéis' => route('roles.index'),
            'Permissões' => route('permissions.index'),
            'Pedidos' => route('register.orders.index'),
            'Aprovações' => route('register.approvals.index'),
        ]);
    }

    #[Override]
    public function shouldRender(): bool
    {
        /** @var User $user */
        $user = Auth::user();

        return
            $user->email === config('app.superadmin.email') ||
            $user->hasRole(RoleNameEnum::SUPER_ADMIN->value);
    }

    #[Override]
    public function render(): View
    {
        return view('components.molecules.accordion-menu', [
            'label' => 'Administração',
            'items' => parent::render()->render(),
        ]);
    }
}
