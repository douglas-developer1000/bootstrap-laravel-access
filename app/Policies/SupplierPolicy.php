<?php

declare(strict_types=1);

namespace App\Policies;

use App\Libraries\Enums\PermissionNameEnum;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Auth\Access\Response;

final class SupplierPolicy
{
    /**
     * Determine whether the user can view any models.
     * @see ../../routes/custom/suppliers.php
     */
    public function viewAny(User $user): bool
    {
        return $user->can(PermissionNameEnum::SUPPLIER_INDEX);
    }

    /**
     * Determine whether the user can create models.
     * @see ../../routes/custom/suppliers.php
     */
    public function create(User $user): bool
    {
        return $user->can(PermissionNameEnum::SUPPLIER_CREATE);
    }

    /**
     * Determine whether the user can view the model.
     * @see ../../routes/custom/suppliers.php
     */
    public function view(User $user, Supplier $supplier): bool
    {
        return (
            $user->can(PermissionNameEnum::SUPPLIER_SHOW) && (
                $user->isModelMine($supplier) ||
                $supplier->native == 1
            )
        );
    }

    /**
     * Determine whether the user can edit the model.
     * @see ../../routes/custom/suppliers.php
     */
    public function edit(User $user, Supplier $supplier): bool
    {
        return (
            $user->isModelMine($supplier) &&
            $user->can(PermissionNameEnum::SUPPLIER_EDIT)
        );
    }

    /**
     * Determine whether the user can store the model.
     * @see ../../routes/custom/suppliers.php
     */
    public function store(User $user): bool
    {
        return $user->can(PermissionNameEnum::SUPPLIER_STORE);
    }

    /**
     * Determine whether the user can update the model.
     * @see ../../routes/custom/suppliers.php
     */
    public function update(User $user, Supplier $supplier): bool
    {
        return (
            $user->isModelMine($supplier) &&
            $user->can(PermissionNameEnum::SUPPLIER_UPDATE)
        );
    }

    /**
     * Determine whether the user can delete the model.
     * @see ../../routes/custom/suppliers.php
     */
    public function delete(User $user, Supplier $supplier): bool
    {
        return (
            $user->isModelMine($supplier) &&
            !$supplier->deleted_at
        ) && $user->can(PermissionNameEnum::SUPPLIER_DESTROY);
    }

    /**
     * Determine whether the user can delete the model list.
     * @see ../../routes/custom/suppliers.php
     */
    public function deleteList(User $user, array $supplierList): bool
    {
        return collect($supplierList)->every(fn(Supplier $supplier) => (
            $this->delete($user, $supplier)
        ));
    }

    /**
     * Determine whether the user can restore the model.
     * @see ../../routes/custom/suppliers.php
     */
    public function restore(User $user, Supplier $supplierDeleted): bool
    {
        return (
            $user->isModelMine($supplierDeleted) &&
            $supplierDeleted->deleted_at
        ) && $user->can(PermissionNameEnum::SUPPLIER_RESTORE);
    }

    /**
     * Determine whether the user can restore the model list.
     * 
     * @param Supplier[] $supplierList
     * @see ../../routes/custom/suppliers.php
     */
    public function restoreList(User $user, array $supplierList): bool
    {
        return collect($supplierList)->every(fn(Supplier $supplier) => (
            $this->restore($user, $supplier)
        ));
    }
}
