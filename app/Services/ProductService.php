<?php

declare(strict_types=1);

namespace App\Services;

use App\Libraries\Traits\InputPickerTrait;
use App\Libraries\Traits\PicRequestHandleTrait;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Closure;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

final class ProductService
{
    use InputPickerTrait, PicRequestHandleTrait;

    protected function getProdCategoryParam(Request $request, User $user): int|string
    {
        if ($user->cannot('viewAny', ProductCategory::class)) {
            return ProductCategory::getAnonymous()->id;
        }

        return $request->input('category');
    }

    public function extractProductParams(Request $request, ?Product $product = null): array
    {
        /** @var User $user */
        $user = Auth::user();

        return $this->attachImgInput(
            $this->pickInputs(
                $request,
                [
                    'name' => $request->input('name'),
                    'product_category_id' => $this->getProdCategoryParam(
                        $request,
                        $user
                    ),
                    'details' => $request->input('details'),
                    'user_id' => $user->id,
                ],
                'obs',
            ),
            $request,
            \strval($user->id),
            'img',
            $product
        );
    }

    /**
     * @param  array{name: string, obs: ?string, img: ?string, details: ?string, product_category_id: int|string }  $data
     */
    public function createProduct(array $data): ?Product
    {
        return Product::create($data);
    }

    public function updateProduct(array $params, Product $product)
    {
        $product->update($params);
    }

    public function removeProduct(Product $product)
    {
        if ($product->stockEntries()->count('id') > 0) {
            $product->delete();
        } else {
            $this->removeStoredImg('img', $product);
            $product->forceDelete();
        }
    }

    public function restoreProduct(Product $product)
    {
        $product->restore();
    }

    /**
     * @param  Product[]  $products
     */
    public function restoreProductGroup(array $products)
    {
        collect($products)->each($this->restoreProduct(...));
    }

    /**
     * @param  Product[]  $products
     */
    public function removeProductList(array $products): void
    {
        collect($products)->each($this->removeProduct(...));
    }

    /**
     * @param  Closure(Builder): Builder  $callback
     */
    public function queryProduct(bool $deleted = false, string $alias = 'products', ?Closure $callback = null): Builder
    {
        $column = (new Product())->getDeletedAtColumn();

        return DB::table('products', $alias)
            ->when(
                \is_callable($callback),
                $callback(...)
            )
            ->when(
                $deleted,
                fn (Builder $query) => $query->whereNotNull("{$alias}.{$column}")
            )
            ->when(
                ! $deleted,
                fn (Builder $query) => $query->whereNull("{$alias}.{$column}")
            );
    }
}
