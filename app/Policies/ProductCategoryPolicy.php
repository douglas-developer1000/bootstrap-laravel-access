<?php

declare(strict_types=1);

namespace App\Policies;

use App\Libraries\Enums\PermissionNameEnum;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Auth\Access\Response;

final class ProductCategoryPolicy
{
    /**
     * Determine whether the user can view any models.
     * @see ../../routes/custom/productCategories.php
     */
    public function viewAny(User $user): bool
    {
        return $user->can(PermissionNameEnum::PRODUCT_CATEGORY_INDEX);
    }

    /**
     * Determine whether the user can create models.
     * @see ../../routes/custom/productCategories.php
     */
    public function create(User $user): bool
    {
        return $user->can(PermissionNameEnum::PRODUCT_CATEGORY_CREATE);
    }

    /**
     * Determine whether the user can view the model.
     * @see ../../routes/custom/productCategories.php
     */
    public function view(User $user, ProductCategory $category): bool
    {
        return $user->can(PermissionNameEnum::PRODUCT_CATEGORY_SHOW) && (
            $user->isModelMine($category)
        );
    }

    /**
     * Determine whether the user can edit the model.
     * @see ../../routes/custom/productCategories.php
     */
    public function edit(User $user, ProductCategory $category): bool
    {
        return (
            $user->isModelMine($category) &&
            $user->can(PermissionNameEnum::PRODUCT_CATEGORY_EDIT)
        );
    }

    /**
     * Determine whether the user can store the model.
     * @see ../../routes/custom/productCategories.php
     */
    public function store(User $user): bool
    {
        return $user->can(PermissionNameEnum::PRODUCT_CATEGORY_STORE);
    }

    /**
     * Determine whether the user can update the model.
     * @see ../../routes/custom/productCategories.php
     */
    public function update(User $user, ProductCategory $category): bool
    {
        return $user->isModelMine($category) && (
            $user->can(PermissionNameEnum::PRODUCT_CATEGORY_UPDATE)
        );
    }

    /**
     * Determine whether the user can delete the model.
     * @see ../../routes/custom/productCategories.php
     */
    public function delete(User $user, ProductCategory $category): bool
    {
        return (
            $user->isModelMine($category) &&
            !$category->deleted_at &&
            $user->can(PermissionNameEnum::PRODUCT_CATEGORY_DESTROY)
        );
    }

    /**
     * Determine whether the user can delte the model list.
     * @see ../../routes/custom/productCategories.php
     */
    public function deleteList(User $user, array $productCategoryList): bool
    {
        return collect($productCategoryList)->every(fn(ProductCategory $category) => (
            $this->delete($user, $category)
        ));
    }

    /**
     * Determine whether the user can restore the model.
     * @see ../../routes/custom/productCategories.php
     */
    public function restore(User $user, ProductCategory $prodCategoryDeleted): bool
    {
        return (
            $prodCategoryDeleted->deleted_at &&
            $user->isModelMine($prodCategoryDeleted)
        ) && $user->can(PermissionNameEnum::PRODUCT_CATEGORY_RESTORE);
    }

    /**
     * Determine whether the user can restore the model list.
     * @see ../../routes/custom/productCategories.php
     */
    public function restoreList(User $user, array $productCategoryList): bool
    {
        return collect($productCategoryList)->every(fn(ProductCategory $category) => (
            $this->restore($user, $category)
        ));
    }
}
