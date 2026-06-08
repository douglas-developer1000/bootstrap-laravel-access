<?php

namespace App\Policies;

use App\Libraries\Enums\PermissionNameEnum;
use App\Models\Discount;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DiscountPolicy
{
    /**
     * Determine whether the user can view any models.
     * @see ../../routes/custom/discounts.php
     */
    public function viewAny(User $user): bool
    {
        return $user->can(PermissionNameEnum::DISCOUNT_INDEX);
    }

    /**
     * Determine whether the user can create models.
     * @see ../../routes/custom/discounts.php
     */
    public function create(User $user): bool
    {
        return $user->can(PermissionNameEnum::DISCOUNT_CREATE);
    }

    /**
     * Determine whether the user can view the model.
     * @see ../../routes/custom/discounts.php
     */
    public function view(User $user, Discount $discount): bool
    {
        return (
            $user->isModelMine($discount) &&
            $user->can(PermissionNameEnum::DISCOUNT_SHOW)
        );
    }

    /**
     * Determine whether the user can edit the model.
     * @see ../../routes/custom/discounts.php
     */
    public function edit(User $user, Discount $discount): bool
    {
        return (
            $user->isModelMine($discount) &&
            $user->can(PermissionNameEnum::DISCOUNT_EDIT)
        );
    }

    /**
     * Determine whether the user can store the model.
     * @see ../../routes/custom/discounts.php
     */
    public function store(User $user): bool
    {
        return $user->can(PermissionNameEnum::DISCOUNT_STORE);
    }

    /**
     * Determine whether the user can update the model.
     * @see ../../routes/custom/discounts.php
     */
    public function update(User $user, Discount $discount): bool
    {
        return (
            $user->isModelMine($discount) &&
            $user->can(PermissionNameEnum::DISCOUNT_UPDATE)
        );
    }

    /**
     * Determine whether the user can delete the model list.
     *
     * @param Discount[] $discountList
     * @see ../../routes/custom/discounts.php
     */
    public function deleteList(User $user, array $discountList): bool
    {
        return collect($discountList)->every(fn(Discount $discount) => (
            $user->isModelMine($discount) &&
            !$discount->deleted_at
        )) && $user->can(PermissionNameEnum::DISCOUNT_DESTROY_GROUP);
    }

    /**
     * Determine whether the user can delete the model.
     * @see ../../routes/custom/discounts.php
     */
    public function delete(User $user, Discount $discount): bool
    {
        return (
            !$discount->deleted_at &&
            $user->isModelMine($discount)
        ) && $user->can(PermissionNameEnum::DISCOUNT_RESTORE);
    }

    /**
     * Determine whether the user can restore the model.
     * @see ../../routes/custom/discounts.php
     */
    public function restore(User $user, Discount $discountDeleted): bool
    {
        return (
            $user->isModelMine($discountDeleted) &&
            $discountDeleted->deleted_at
        ) && $user->can(PermissionNameEnum::DISCOUNT_RESTORE);
    }

    /**
     * Determine whether the user can restore the model list.
     * 
     * @param Discount[] $discountList
     * @see ../../routes/custom/discounts.php
     */
    public function restoreList(User $user, array $discountList): bool
    {
        return collect($discountList)->every(fn(Discount $discount) => (
            $user->isModelMine($discount) &&
            $discount->deleted_at
        )) && $user->can(PermissionNameEnum::DISCOUNT_RESTORE_GROUP);
    }
}
