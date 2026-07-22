<?php

declare(strict_types=1);

namespace App\Services;

use App\Facades\ListStorager;
use App\Models\Product;
use App\Models\User;
use App\Services\Abstracts\AbstractPaginatorIndex;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Override;

final class StockService
{
    protected User $user;

    public function __construct(
        protected StockEntryService $stockEntrySvc,
        protected ProductService $prodSvc
    ) {
        $this->user = Auth::user();
    }

    public function prepareIndex(Request $request): LengthAwarePaginator
    {
        return (new class($this->user, $this->stockEntrySvc, $this->prodSvc) extends AbstractPaginatorIndex
        {
            public function __construct(
                protected User $user,
                protected StockEntryService $stockEntrySvc,
                protected ProductService $prodSvc,
            ) {
                parent::__construct();
            }

            #[Override]
            public function query(Request $request): Builder
            {
                $trashed = $request->boolean('trashed');

                return $this->prodSvc->queryProduct(
                    deleted: $trashed,
                    alias: 'p',
                    callback: fn ($query) => $query->where(
                        ['p.user_id' => $this->user->id]
                    )
                )->select([
                    'p.id',
                    'p.name',
                    'p.user_id',
                    'p.deleted_at',
                ]);
            }

            #[Override]
            public function attachQuery(Request $request, Builder $query): Builder
            {
                return parent::attachQuery(
                    $request,
                    $this->filterExits(
                        $request,
                        $this->stockEntrySvc->joinStockEntriesQtySummarized(
                            $this->joinCategory($query),
                            'p',
                        )
                            ->groupBy([
                                'id',
                                'name',
                                'user_id',
                                'deleted_at',
                                'catId',
                                'catName',
                            ])
                    )
                );
            }

            protected function filterExits(Request $request, Builder $query): Builder
            {
                return $query->when(
                    $request->boolean('exits'),
                    fn (Builder $query) => $query->whereIn(
                        'p.id',
                        ListStorager::getList('productsToExit')
                    )
                );
            }

            protected function joinCategory(Builder $query): Builder
            {
                return $query->join(
                    'product_categories as cat',
                    'p.product_category_id',
                    '=',
                    'cat.id'
                )
                    ->addSelect('cat.id as catId')
                    ->addSelect('cat.name as catName');
            }

            #[Override]
            public function getSortColumns(): array
            {
                return ['catName', 'name', 'qtyRemain'];
            }
        })->prepareIndex(
            $request,
            'id',
            'name',
            'user_id',
            'deleted_at',
            'catId',
            'catName',
            'qtyRemain'
        );
    }

    public function hydrateStocks(array $stocks): Collection
    {
        $productsToExit = collect(ListStorager::getList('productsToExit'));

        return Product::hydrate($stocks)->map(function (Product $product, int $i) use (&$stocks, &$productsToExit) {
            $product->catId = $stocks[$i]->catId;
            $product->catName = $stocks[$i]->catName;
            $product->qtyRemain = $stocks[$i]->qtyRemain;
            $product->inSalesCart = $productsToExit->contains($product->id);

            return $product;
        });
    }

    public function getProductRemainQty(Product $product): int
    {
        return \intval(
            $this->stockEntrySvc->joinStockEntriesQtySummarized(
                $this->prodSvc->queryProduct(
                    deleted: false,
                    alias: 'p',
                    callback: fn ($query) => $query->where(
                        ['p.user_id' => $this->user->id]
                    )
                ),
                'p'
            )->where('p.id', '=', $product->id)->first()->qtyRemain
        );
    }
}
