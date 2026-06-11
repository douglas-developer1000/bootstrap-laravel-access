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
use Illuminate\Database\Eloquent\Collection;

final class StockExitPolicy
{
    public function __construct(
        protected StockService $stockSvc,
        protected ProductToExitHandlerService $productToExitSvc
    ) {
        // ...
    }

    /**
     * @return Collection<Product>
     */
    protected function productsToExitModels(): Collection
    {
        return Product::findMany(
            $this->productToExitSvc->getProductsToExit()
        );
    }

    public function showPersonalUse(User $user): bool
    {
        return (
            $user->can(PermissionNameEnum::PERSONAL_USE_EXIT_SHOW)
        );
    }

    public function showDemonstration(User $user): bool
    {
        return (
            $user->can(PermissionNameEnum::DEMONSTRATION_EXIT_SHOW)
        );
    }

    public function showLoss(User $user): bool
    {
        return (
            $user->can(PermissionNameEnum::LOSS_EXIT_SHOW)
        );
    }

    public function showRaw(User $user): bool
    {
        return (
            $user->can(PermissionNameEnum::RAW_EXIT_SHOW)
        );
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
        if ($exitType === StockExitTypeEnum::EXCHANGE) {
            return (
                $productsToExit->isNotEmpty() &&
                $user->can(PermissionNameEnum::EXCHANGE_EXIT_CREATE)
            );
        }
        if ($exitType === StockExitTypeEnum::PERSONAL_USE) {
            return (
                $productsToExit->isNotEmpty() &&
                $user->can(PermissionNameEnum::PERSONAL_USE_EXIT_CREATE)
            );
        }
        if ($exitType === StockExitTypeEnum::DEMONSTRATION) {
            return (
                $productsToExit->isNotEmpty() &&
                $user->can(PermissionNameEnum::DEMONSTRATION_EXIT_CREATE)
            );
        }
        if ($exitType === StockExitTypeEnum::LOSS) {
            return (
                $productsToExit->isNotEmpty() &&
                $user->can(PermissionNameEnum::LOSS_EXIT_CREATE)
            );
        }
        return (
            $productsToExit->isNotEmpty() &&
            $exitType === StockExitTypeEnum::RAW &&
            $user->can(PermissionNameEnum::RAW_EXIT_CREATE)
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
            $user->can(PermissionNameEnum::SALE_EXIT_CREATE)
        );
    }

    /**
     * Determine whether the user can create models.
     * @see ../../routes/custom/stocks.php
     */
    public function store(User $user, StockExitTypeEnum $exitType): bool
    {
        if ($exitType === StockExitTypeEnum::SALE) {
            return (
                $user->can(PermissionNameEnum::SALE_EXIT_STORE) &&
                $this->productsToExitModels()->every(
                    fn(Product $product) => $user->isModelMine($product) && !$product->deleted_at
                )
            );
        }
        if ($exitType === StockExitTypeEnum::EXCHANGE) {
            return (
                $user->can(PermissionNameEnum::EXCHANGE_EXIT_STORE) &&
                $this->productsToExitModels()->every(
                    fn(Product $product) => $user->isModelMine($product) && !$product->deleted_at
                )
            );
        }
        if ($exitType === StockExitTypeEnum::PERSONAL_USE) {
            return (
                $user->can(PermissionNameEnum::PERSONAL_USE_EXIT_STORE) &&
                $this->productsToExitModels()->every(
                    fn(Product $product) => $user->isModelMine($product) && !$product->deleted_at
                )
            );
        }
        if ($exitType === StockExitTypeEnum::DEMONSTRATION) {
            return (
                $user->can(PermissionNameEnum::DEMONSTRATION_EXIT_STORE) &&
                $this->productsToExitModels()->every(
                    fn(Product $product) => $user->isModelMine($product) && !$product->deleted_at
                )
            );
        }
        if ($exitType === StockExitTypeEnum::LOSS) {
            return (
                $user->can(PermissionNameEnum::LOSS_EXIT_STORE) &&
                $this->productsToExitModels()->every(
                    fn(Product $product) => $user->isModelMine($product) && !$product->deleted_at
                )
            );
        }
        return (
            $user->can(PermissionNameEnum::RAW_EXIT_STORE) &&
            $this->productsToExitModels()->every(
                fn(Product $product) => $user->isModelMine($product) && !$product->deleted_at
            )
        );
    }

    /**
     * Determine whether the user can view any garbage models.
     */
    public function viewGarbageAny(User $user): bool
    {
        return $user->can(PermissionNameEnum::GARBAGE_INDEX);
    }

    /**
     * Determine whether the user can view any raw stock exit models.
     */
    public function viewRawExitAny(User $user): bool
    {
        return $user->can(PermissionNameEnum::RAW_EXIT_INDEX);
    }

    /**
     * Determine whether the user can destroy models.
     * @see ../../routes/custom/stocks.php
     */
    public function delete(User $user, StockExit $exit): bool
    {
        if ($exit->type === StockExitTypeEnum::PERSONAL_USE) {
            return (
                $user->isModelMine($exit) &&
                $user->can(PermissionNameEnum::PERSONAL_USE_EXIT_DESTROY)
            );
        }
        if ($exit->type === StockExitTypeEnum::DEMONSTRATION) {
            return (
                $user->isModelMine($exit) &&
                $user->can(PermissionNameEnum::DEMONSTRATION_EXIT_DESTROY)
            );
        }
        if ($exit->type === StockExitTypeEnum::LOSS) {
            return (
                $user->isModelMine($exit) &&
                $user->can(PermissionNameEnum::LOSS_EXIT_DESTROY)
            );
        }
        return (
            $user->isModelMine($exit) &&
            $user->can(PermissionNameEnum::RAW_EXIT_DESTROY)
        );
    }

    public function deleteList(User $user, array $stockExitList): bool
    {
        return (
            collect($stockExitList)->every(fn(StockExit $exit) => (
                $this->delete($user, $exit)
            ))
        );
    }
}
