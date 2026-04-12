<?php

declare(strict_types=1);

namespace App\View\Components\Molecules;

use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use Closure;

class SuperuserMenuItem extends Component
{
    protected $url;

    protected $menuData;

    public function __construct()
    {
        $this->url = url()->current();
        $this->menuData = [
            'Usuários' => route('users.index'),
            'Papéis' => route('roles.index'),
            'Permissões' => route('permissions.index')
        ];
    }

    public function render(): View|Closure|string
    {
        return view('components.organisms.superuser-menu-items', [
            'menuItems' => $this->menuData,
            'url' => $this->url
        ]);
    }
}
