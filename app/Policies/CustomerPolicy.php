<?php

declare(strict_types=1);

namespace App\Policies;

use App\Libraries\Enums\PermissionNameEnum;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Auth\Access\Response;

final class CustomerPolicy
{
    /**
     * Determine whether the user can view any models.
     * @see ../../routes/custom/customers.php
     */
    public function viewAny(User $user): bool
    {
        return $user->can(PermissionNameEnum::CUSTOMER_INDEX);
    }

    /**
     * Determine whether the user can create models.
     * @see ../../routes/custom/customers.php
     */
    public function create(User $user): bool
    {
        return $user->can(PermissionNameEnum::CUSTOMER_CREATE);
    }

    /**
     * Determine whether the user can view the model.
     * @see ../../routes/custom/customers.php
     */
    public function view(User $user, Customer $customer): bool
    {
        return (
            $user->isModelMine($customer) &&
            $user->can(PermissionNameEnum::CUSTOMER_SHOW)
        );
    }

    /**
     * Determine whether the user can edit the model.
     * @see ../../routes/custom/customers.php
     */
    public function edit(User $user, Customer $card): bool
    {
        return (
            $user->isModelMine($card) &&
            $user->can(PermissionNameEnum::CUSTOMER_EDIT)
        );
    }

    /**
     * Determine whether the user can store the model.
     * @see ../../routes/custom/customers.php
     */
    public function store(User $user): bool
    {
        return $user->can(PermissionNameEnum::CUSTOMER_STORE);
    }

    /**
     * Determine whether the user can update the model.
     * @see ../../routes/custom/customers.php
     */
    public function update(User $user, Customer $customer): bool
    {
        return (
            $user->isModelMine($customer) &&
            $user->can(PermissionNameEnum::CUSTOMER_UPDATE)
        );
    }

    /**
     * Determine whether the user can delete the model.
     * @see ../../routes/custom/customers.php
     */
    public function delete(User $user, Customer $customer): bool
    {
        return (
            $user->isModelMine($customer) &&
            !$customer->deleted_at
        ) && $user->can(PermissionNameEnum::CUSTOMER_DESTROY);
    }

    /**
     * Determine whether the user can delete the model list.
     *
     * @param Customer[] $customerList
     * @see ../../routes/custom/customers.php
     */
    public function deleteList(User $user, array $customerList): bool
    {
        return collect($customerList)->every(fn(Customer $customer) => (
            $this->delete($user, $customer)
        ));
    }

    /**
     * Determine whether the user can restore the model.
     * @see ../../routes/custom/customers.php
     */
    public function restore(User $user, Customer $customerDeleted): bool
    {
        return (
            $user->isModelMine($customerDeleted) &&
            $customerDeleted->deleted_at
        ) && $user->can(PermissionNameEnum::CUSTOMER_RESTORE);
    }

    /**
     * Determine whether the user can restore the model list.
     * 
     * @param Customer[] $customerList
     * @see ../../routes/custom/customers.php
     */
    public function restoreList(User $user, array $customerList): bool
    {
        return collect($customerList)->every(fn(Customer $customer) => (
            $user->isModelMine($customer) &&
            $customer->deleted_at
        ));
    }
}
