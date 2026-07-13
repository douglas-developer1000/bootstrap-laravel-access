<?php

declare(strict_types=1);

namespace App\Policies;

use App\Libraries\Enums\PermissionNameEnum;
use App\Models\Product;
use App\Models\User;
use App\View\Components\Molecules\UserMenuItems;

final class ProductPolicy
{
    /**
     * Determine whether the user can view any models.
     *
     * @see ../../routes/custom/stocks.php
     * @see UserMenuItems::__construct()
     */
    public function viewAny(User $user): bool
    {
        return $user->can(PermissionNameEnum::PRODUCT_INDEX);
    }

    public function show(User $user, Product $product): bool
    {
        return
            $user->isModelMine($product) &&
            $user->can(PermissionNameEnum::PRODUCT_SHOW);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can(PermissionNameEnum::PRODUCT_CREATE);
    }

    /**
     * Determine whether the user can edit the model.
     *
     * @see ../../routes/custom/products.php
     */
    public function edit(User $user, Product $product): bool
    {
        return
            $user->isModelMine($product) &&
            $user->can(PermissionNameEnum::PRODUCT_EDIT);
    }

    /**
     * Determine whether the user can store a model.
     *
     * @see ../../routes/custom/products.php
     */
    public function store(User $user): bool
    {
        return $user->can(PermissionNameEnum::PRODUCT_STORE);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @see ../../routes/custom/products.php
     */
    public function update(User $user, Product $product): bool
    {
        return
            $user->isModelMine($product) &&
            $user->can(PermissionNameEnum::PRODUCT_UPDATE);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @see ../../routes/custom/products.php
     */
    public function delete(User $user, Product $product): bool
    {
        return (
            ! $product->deleted_at &&
            $user->isModelMine($product)
        ) && $user->can(PermissionNameEnum::PRODUCT_DESTROY);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @see ../../routes/custom/products.php
     */
    public function deleteList(User $user, array $productList): bool
    {
        return collect($productList)->every(fn (Product $product) => (
            $this->delete($user, $product)
        ));
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @see ../../routes/custom/products.php
     */
    public function restore(User $user, Product $productDeleted): bool
    {
        return (
            $user->isModelMine($productDeleted) &&
            $productDeleted->deleted_at
        ) && $user->can(PermissionNameEnum::PRODUCT_RESTORE);
    }

    /**
     * Determine whether the user can restore the model list.
     *
     * @param  Product[]  $productList
     *
     * @see ../../routes/custom/products.php
     */
    public function restoreList(User $user, array $productList): bool
    {
        return collect($productList)->every(fn (Product $product) => (
            $this->restore($user, $product)
        ));
    }

    public function useOnExit(User $user, Product $product): bool
    {
        return $user->isModelMine($product);
    }
}
