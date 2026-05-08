<?php

declare(strict_types=1);

namespace App\View\Components\Molecules;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

abstract class MenuItem extends Component
{
    protected string $url;
    protected array $menuData;

    public function __construct(array $menuData)
    {
        $this->url = url()->current();
        $this->menuData = $this->filterMenuPermissions($menuData);
    }

    protected function filterMenuPermissions(array $menuData): array
    {
        /**
         * @var \App\Models\User $user
         */
        $user = Auth::user();

        return collect($menuData)->map(function (string|array $data) use (&$user) {
            if (\is_array($data)) {
                [$href, $permission] = $data;
                if (
                    !$user->hasPermissionTo($permission) &&
                    !$user->hasRole('super-admin')
                ) {
                    return NULL;
                }
                return $href;
            }
            return $data;
        })->filter(fn($data) => $data !== NULL)->all();
    }

    public function render()
    {
        return view('components.molecules.header-menu-item', [
            'menuItems' => $this->menuData,
            'url' => $this->url
        ]);
    }
}
