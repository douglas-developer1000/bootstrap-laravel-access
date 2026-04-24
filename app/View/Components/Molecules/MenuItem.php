<?php

declare(strict_types=1);

namespace App\View\Components\Molecules;

use Illuminate\View\Component;

abstract class MenuItem extends Component
{
    protected $url;

    public function __construct(protected array $menuData)
    {
        $this->url = url()->current();
    }

    public function render()
    {
        return view('components.organisms.header-menu-item', [
            'menuItems' => $this->menuData,
            'url' => $this->url
        ]);
    }
}
