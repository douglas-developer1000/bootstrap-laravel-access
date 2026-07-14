<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ProductCategory;
use App\Models\User;
use App\Services\Abstracts\AbstractPaginatorIndex;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Override;

final class ProductCategoryService
{
    protected User $user;

    public function __construct()
    {
        $this->user = Auth::user();
    }

    public function prepareIndex(Request $request): LengthAwarePaginator
    {
        return (new class($this->user) extends AbstractPaginatorIndex
        {
            public function __construct(protected User $user)
            {
                parent::__construct();
            }

            #[Override]
            public function query(Request $request): Builder
            {
                return ProductCategory::whereBelongsTo($this->user)
                    ->getQuery();
            }

            #[Override]
            public function attachQuery(Request $request, Builder $query): Builder
            {
                $trashed = $request->boolean('trashed');
                $deletedColumn = (new ProductCategory())->getDeletedAtColumn();
                if ($trashed) {
                    return parent::attachQuery(
                        $request,
                        $query->whereNotNull($deletedColumn)
                    );
                }

                return parent::attachQuery(
                    $request,
                    $query->whereNull($deletedColumn)
                );
            }

            #[Override]
            public function getSortColumns(): array
            {
                return ['created_at', 'name'];
            }
        })->prepareIndex(
            $request,
            '*'
        );
    }

    /**
     * @return array{name: string, parent_id: int|null}
     */
    public function extractProductCategoryParams(Request $request)
    {
        $inheritance = $request->input('inheritance');

        return [
            'name' => $request->input('name'),
            'parent_id' => $inheritance ? \intval($inheritance) : $inheritance,
        ];
    }

    /**
     * Create a new ProductCategory instance.
     *
     * @param  array{name: string, parent_id: string|int|null}  $data
     */
    public function createCategory(array $data): ?ProductCategory
    {
        return ProductCategory::create([
            ...$data,
            'user_id' => $this->user->id,
        ]);
    }

    public function updateCategory(int $id, array $data)
    {
        return ProductCategory::where(['id' => $id])->update($data);
    }

    public function getProductCategories(array $columns, array $except = [], ?ProductCategory $category = null): Collection
    {
        if ($this->user->cannot('viewAny', ProductCategory::class)) {
            return collect([ProductCategory::firstOrCreate(ProductCategory::getAnonymousFields())]);
        }
        $query = ProductCategory::whereBelongsTo($this->user)
            ->whereNotIn('id', $except)
            ->select($columns);

        if ($category?->parent_id) {
            $hasParentDeleted = ProductCategory::withTrashed()->where([
                'id' => $category->parent_id,
            ])->whereNotNull('deleted_at')->exists();

            if ($hasParentDeleted) {
                $query = $query->union(
                    ProductCategory::onlyTrashed()->where([
                        'id' => $category->parent_id,
                    ])->select($columns)
                );
            }
        }

        return $query->get();
    }

    public function removeProductCategory(ProductCategory $category)
    {
        $qtyProducts = $category->products()->count();
        $qtyChildren = $category->children()->count();
        if ($qtyProducts > 0 || $qtyChildren > 0) {
            $category->delete();
        } else {
            $category->forceDelete();
        }
    }

    public function getAncestorCategoryNames(ProductCategory|int $arg)
    {
        $list = collect([]);
        $category = \is_int($arg) ? ProductCategory::withTrashed()->find($arg) : $arg;
        while ($category) {
            $list->prepend($category->name);
            $category = $category->parent_id ? ProductCategory::withTrashed()->where([
                'id' => $category->parent_id,
            ])->first(['name', 'parent_id']) : null;
        }

        return $list->all();
    }

    /**
     * @param  ProductCategory[]  $categories
     */
    public function removeProductCategoryList(array $categories): void
    {
        collect($categories)->each($this->removeProductCategory(...));
    }

    protected function collectChildrenIds(Collection $categories, &$ids = [])
    {
        foreach ($categories as $category) {
            $ids[] = $category->id;

            if ($category->children->isNotEmpty()) {
                $this->collectChildrenIds($category->children, $ids);
            }
        }

        return $ids;
    }

    public function getChildIds(ProductCategory $category): array
    {
        return $this->collectChildrenIds($category->allChildren()->get());
    }

    public function restoreProductCategory(ProductCategory $category)
    {
        $category->restore();
    }

    public function restoreProductCategoryGroup(array $categories): void
    {
        collect($categories)->each(fn ($category) => $this->restoreProductCategory($category));
    }

    public function hydrateProductCategory(array $productCategories): Collection
    {
        return ProductCategory::hydrate($productCategories);
    }
}
