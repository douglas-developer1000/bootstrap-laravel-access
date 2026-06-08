<?php

declare(strict_types=1);

namespace App\Policies;

use App\Libraries\Enums\PermissionNameEnum;
use App\Libraries\Enums\StockExitTypeEnum;
use App\Models\Customer;
use App\Models\Product;
use App\Models\StockExit;
use App\Models\User;
use App\Services\ProductToExitHandlerService;
use App\Services\StockService;
use Illuminate\Auth\Access\Response;

final class StockExitPolicy
{
    public function __construct(
        protected StockService $stockSvc,
        protected ProductToExitHandlerService $productToExitSvc
    ) {
        // ...
    }

    /**
     * Determine whether the user can mark products to stock exit.
     * @see ../../routes/custom/stocks.php
     */
    public function mark(User $user, Product $product): bool
    {
        return (
            $user->isModelMine($product) &&
            !$product->deleted_at &&
            $this->stockSvc->getProductRemainQty($product) > 0 &&
            !collect($this->productToExitSvc->getProductsToExit())->contains(
                $product->id
            )
        );
    }

    /**
     * Determine whether the user can unmark products to stock exit.
     * @see ../../routes/custom/stocks.php
     */
    public function unmark(User $user, Product $product): bool
    {
        return (
            $user->isModelMine($product) &&
            !$product->deleted_at &&
            $this->stockSvc->getProductRemainQty($product) > 0 &&
            collect($this->productToExitSvc->getProductsToExit())->contains(
                $product->id
            )
        );
    }

    /**
     * Determine whether the user can create models.
     * @see ../../routes/custom/stocks.php
     */
    public function createExit(User $user, StockExitTypeEnum $exitType): bool
    {
        $productsToExit = collect(
            $this->productToExitSvc->getProductsToExit()
        );

        return (
            $productsToExit->isNotEmpty() &&
            collect([
                StockExitTypeEnum::PERSONAL_USE,
                StockExitTypeEnum::EXCHANGE,
                StockExitTypeEnum::DEMONSTRATION,
                StockExitTypeEnum::LOSS,
            ])->contains($exitType) &&
            $user->can(PermissionNameEnum::STOCK_EXIT_CREATE)
        );
    }

    public function createSaleExit(User $user, StockExitTypeEnum $exitType, Customer $customer): bool
    {
        $productsToExit = collect(
            $this->productToExitSvc->getProductsToExit()
        );

        return (
            $productsToExit->isNotEmpty() &&
            $exitType === StockExitTypeEnum::SALE &&
            $user->isModelMine($customer) &&
            $user->can(PermissionNameEnum::STOCK_EXIT_CREATE)
        );
    }

    /**
     * Determine whether the user can create models.
     * @see ../../routes/custom/stocks.php
     */
    public function store(User $user): bool
    {
        $products = Product::findMany(
            $this->productToExitSvc->getProductsToExit()
        );

        return (
            $products->every(
                fn(Product $product) => $user->isModelMine($product) && !$product->deleted_at
            ) &&
            $user->can(PermissionNameEnum::STOCK_EXIT_STORE)
        );
    }

    /**
     * Determine whether the user can view any Exchange models.
     */
    public function viewLossAny(User $user): bool
    {
        return $user->can(PermissionNameEnum::LOSS_INDEX);
    }

    /**
     * Determine whether the user can destroy models.
     * @see ../../routes/custom/stocks.php
     */
    public function deleteLoss(User $user, StockExit $exit): bool
    {
        return (
            $user->isModelMine($exit) &&
            (
                $exit->type === StockExitTypeEnum::LOSS ||
                $exit->type === StockExitTypeEnum::PERSONAL_USE ||
                $exit->type === StockExitTypeEnum::DEMONSTRATION
            ) &&
            $user->can(PermissionNameEnum::STOCK_EXIT_DESTROY)
        );
    }

    public function deleteLossList(User $user, array $stockExitList): bool
    {
        return (
            collect($stockExitList)->every(fn(StockExit $exit) => (
                $user->isModelMine($exit) && (
                    $exit->type === StockExitTypeEnum::LOSS ||
                    $exit->type === StockExitTypeEnum::PERSONAL_USE ||
                    $exit->type === StockExitTypeEnum::DEMONSTRATION
                )
            )) &&
            $user->can(PermissionNameEnum::STOCK_EXIT_DESTROY_GROUP)
        );
    }

    /**
     * Determine whether the user can view any Exchange models.
     */
    public function viewExchangeAny(User $user): bool
    {
        return $user->can(PermissionNameEnum::EXCHANGE_INDEX);
    }

    public function deleteExchange(User $user, StockExit $exit): bool
    {
        return (
            $user->isModelMine($exit) &&
            $exit->type === StockExitTypeEnum::EXCHANGE &&
            $user->can(PermissionNameEnum::STOCK_EXIT_DESTROY)
        );
    }

    public function deleteExchangeList(User $user, array $stockExitList): bool
    {
        return (
            collect($stockExitList)->every(fn(StockExit $exit) => (
                $user->isModelMine($exit) &&
                $exit->type === StockExitTypeEnum::EXCHANGE
            )) &&
            $user->can(PermissionNameEnum::STOCK_EXIT_DESTROY_GROUP)
        );
    }
}
