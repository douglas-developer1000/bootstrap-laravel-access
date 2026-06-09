<?php

declare(strict_types=1);

namespace App\Policies;

use App\Libraries\Enums\PermissionNameEnum;
use App\Libraries\Enums\StockExitTypeEnum;
use App\Models\Exchange;
use App\Models\StockExit;
use App\Models\User;
use Illuminate\Auth\Access\Response;

final class ExchangePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can(PermissionNameEnum::EXCHANGE_INDEX);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Exchange $exchange, StockExit $exit): bool
    {
        return (
            $user->isModelMine($exit) &&
            $exchange->stock_exit_id === $exit->id &&
            $exit->type === StockExitTypeEnum::EXCHANGE &&
            $user->can(PermissionNameEnum::EXCHANGE_DESTROY)
        );
    }

    /**
     * Determine whether the user can delete the model list.
     * 
     * Used just by remotion list request.
     */
    public function deleteList(User $user, array $exchangeList): bool
    {
        return (
            collect($exchangeList)->every(fn(Exchange $exchange) => (
                $this->delete($user, $exchange, $exchange->stockExit)
            ))
        );
    }
}
