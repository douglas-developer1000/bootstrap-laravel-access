<?php

declare(strict_types=1);

namespace App\Policies;

use App\Libraries\Enums\PermissionNameEnum;
use App\Models\Product;
use App\Models\StockEntry;
use App\Models\User;
// use Illuminate\Auth\Access\Response;

final class StockEntryPolicy
{
    /**
     * Determine whether the user can create models.
     * @see ../../routes/custom/stocks.php
     */
    public function create(User $user, Product $product): bool
    {
        return (
            $user->isModelMine($product) &&
            !$product->deleted_at &&
            $user->can(PermissionNameEnum::STOCK_ENTRY_CREATE)
        );
    }

    public function store(User $user, Product $product): bool
    {
        return (
            $user->isModelMine($product) &&
            !$product->deleted_at &&
            $user->can(PermissionNameEnum::STOCK_ENTRY_STORE)
        );
    }

    public function spend(User $user, array $entries): bool
    {
        $entriesFromDb = StockEntry::findMany($entries);
        return (
            \count($entries) > 0 &&
            $entriesFromDb->count() === \count($entries) &&
            $entriesFromDb->every(
                fn(StockEntry $entry) => $user->isModelMine($entry)
            ) &&
            $user->can(PermissionNameEnum::STOCK_ENTRY_SPEND)
        );
    }
}
