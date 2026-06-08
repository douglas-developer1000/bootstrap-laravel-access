<?php

declare(strict_types=1);

namespace App\View\Components\Molecules;

use App\Libraries\Enums\RoleNameEnum;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Override;

final class SuperuserMenuItems extends MenuItem
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

    #[Override]
    public function shouldRender()
    {
        /** @var User $user */
        $user = Auth::user();

        return (
            $user->email === config('app.superadmin.email') ||
            $user->hasRole(RoleNameEnum::SUPER_ADMIN->value)
        );
    }

    #[Override]
    public function render()
    {
        return view('components.molecules.accordion-menu', [
            'label' => 'Administração',
            'items' => parent::render()->render()
        ]);
    }
}
