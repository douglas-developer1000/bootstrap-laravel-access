<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Collection;

final class ProductToExitHandlerService
{
    public function getProductsToExit(): array
    {
        session()->put('productsToExit', session()->get('productsToExit', []));
        return session()->get('productsToExit');
    }

    public function clearProductsToExit(): void
    {
        session()->forget('productsToExit');
    }
}
