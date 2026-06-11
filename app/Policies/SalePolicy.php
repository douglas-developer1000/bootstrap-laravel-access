<?php

declare(strict_types=1);

namespace App\Policies;

use App\Libraries\Enums\PermissionNameEnum;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Auth\Access\Response;

final class SalePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can(PermissionNameEnum::SALE_EXIT_INDEX);
    }

    public function show(User $user, Sale $sale)
    {
        return (
            $user->isModelMine($sale) &&
            $user->can(PermissionNameEnum::SALE_EXIT_SHOW)
        );
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Sale $sale): bool
    {
        return (
            $user->isModelMine($sale) &&
            $user->can(PermissionNameEnum::SALE_EXIT_DESTROY)
        );
    }

    /**
     * @param Sale[] $saleList
     */
    public function deleteList(User $user, array $saleList): bool
    {
        return collect($saleList)->every(fn(Sale $sale) => (
            $this->delete($user, $sale)
        ));
    }
}
