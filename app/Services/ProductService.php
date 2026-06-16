<?php

declare(strict_types=1);

namespace App\Services;

use App\Libraries\Traits\InputPickerTrait;
use App\Libraries\Traits\PicRequestHandleTrait;
use App\Models\Product;
use Closure;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

final class ProductService
{
    use PicRequestHandleTrait, InputPickerTrait;

    public function extractProductParams(Request $request, ?Product $product = NULL): array
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return $this->attachImgInput(
            $this->pickInputs(
                $request,
                [
                    'name' => $request->input('name'),
                    'product_category_id' => $request->input('category'),
                    'details' => $request->input('details'),
                    'user_id' => $user->id
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
     * @param array{name: string, obs: ?string, img: ?string, details: ?string, product_category_id: int|string } $data
     * 
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
     * @param Product[] $products
     */
    public function restoreProductGroup(array $products)
    {
        collect($products)->each($this->restoreProduct(...));
    }

    /**
     * @param Product[] $products
     */
    public function removeProductList(array $products): void
    {
        collect($products)->each($this->removeProduct(...));
    }

    /**
     * @param Closure(Builder): Builder $callback
     */
    public function queryProduct(bool $deleted = false, string $alias = 'products', ?Closure $callback = NULL): Builder
    {
        $column = (new Product())->getDeletedAtColumn();

        return DB::table('products', $alias)
            ->when(
                \is_callable($callback),
                $callback(...)
            )
            ->when(
                $deleted,
                fn(Builder $query) => $query->whereNotNull("{$alias}.{$column}")
            )
            ->when(
                !$deleted,
                fn(Builder $query) => $query->whereNull("{$alias}.{$column}")
            );
    }
}
