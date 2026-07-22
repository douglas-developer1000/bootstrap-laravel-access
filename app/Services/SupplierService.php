<?php

declare(strict_types=1);

namespace App\Services;

use App\Facades\Paginator;
use App\Libraries\Traits\InputPickerTrait;
use App\Libraries\Traits\PicRequestHandleTrait;
use App\Libraries\Values\CnpjValue;
use App\Models\Supplier;
use App\Models\User;
use App\Services\Abstracts\AbstractPaginatorIndex;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Override;

final class SupplierService
{
    use InputPickerTrait, PicRequestHandleTrait;

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
                // ...
            }

            #[Override]
            public function query(Request $request): Builder
            {
                return Supplier::whereBelongsTo($this->user)->getQuery();
            }

            #[Override]
            public function attachQuery(Request $request, Builder $query): Builder
            {
                return parent::attachQuery(
                    $request,
                    $this->filterSupplierName(
                        $request,
                        $this->filterDeleted(
                            $request,
                            $this->filterSuppliersOwnership($request, $query)
                        )
                    )
                );
            }

            #[Override]
            public function getSortColumns(): array
            {
                return ['created_at', 'name'];
            }

            protected function filterDeleted(Request $request, Builder $query): Builder
            {
                $deletedColumn = (new Supplier())->getDeletedAtColumn();
                $trashed = $request->boolean('trashed');

                return $query
                    ->when(
                        $trashed,
                        fn (Builder $query) => $query->whereNotNull($deletedColumn)
                    )
                    ->when(
                        ! $trashed,
                        fn (Builder $query) => $query->whereNull($deletedColumn)
                    );
            }

            protected function filterSuppliersOwnership(Request $request, Builder $query): Builder
            {
                return $query->when(
                    ! $request->boolean('own'),
                    fn (Builder $query) => $query->union(
                        Supplier::whereNative(1)->notAnonymous()->getQuery()
                    )
                );
            }

            protected function filterSupplierName(Request $request, Builder $query): Builder
            {
                return $query->when(
                    Paginator::buildSearch($request->only('name'), 'name'),
                    function (Builder $query, string $nameSearch) {
                        $nameSearch = addcslashes($nameSearch, '%_');

                        return $query->whereLike('name', "%{$nameSearch}%");
                    }
                );
            }
        })->prepareIndex(
            $request,
            '*'
        );
    }

    protected function parseCnpj(array $base): array
    {
        $list = collect($base);
        if ($list->has('cnpj')) {
            $list->offsetSet('cnpj', new CnpjValue($list->get('cnpj') ?: null));
        }

        return $list->all();
    }

    public function extractSupplierParams(Request $request, ?Supplier $supplier = null)
    {
        return $this->attachImgInput(
            $this->parseCnpj(
                $this->pickInputs(
                    $request,
                    [
                        'name' => $request->input('name'),
                        'native' => $request->boolean('native'),
                        'user_id' => $this->user->id,
                    ],
                    'color',
                    'obs',
                    'cnpj'
                )
            ),
            $request,
            \strval($this->user->id),
            'img',
            $supplier
        );
    }

    public function createSupplier(array $params)
    {
        Supplier::create($params);
    }

    public function removeSupplier(Supplier $supplier): void
    {
        if ($supplier->stockEntries()->count() > 0) {
            $supplier->delete();
        } else {
            $this->removeStoredImg('img', $supplier);
            $supplier->forceDelete();
        }
    }

    public function updateSupplier(array $params, Supplier $supplier)
    {
        $supplier->update($params);
    }

    public function findSupplierProducts(Supplier $supplier)
    {
        return DB::table('suppliers', 'sup')->where([
            'sup.id' => $supplier->id,
        ])
            ->join(
                'stock_entries',
                'sup.id',
                '=',
                'stock_entries.supplier_id',
            )
            ->join(
                'products',
                'stock_entries.product_id',
                '=',
                'products.id'
            )
            ->join(
                'product_categories',
                'products.product_category_id',
                '=',
                'product_categories.id',
            )
            ->where([
                'products.user_id' => $this->user->id,
            ])
            ->groupBy([
                'prodName',
                'prodCatName',
            ])
            ->get([
                'products.name as prodName',
                'product_categories.name as prodCatName',
            ]);
    }

    /**
     * @param  Supplier[]  $suppliers
     */
    public function removeSupplierGroup(array $suppliers): void
    {
        collect($suppliers)->each($this->removeSupplier(...));
    }

    public function restoreSupplier(Supplier $supplier)
    {
        $supplier->restore();
    }

    public function restoreSupplierGroup(array $suppliers): void
    {
        collect($suppliers)->each($this->restoreSupplier(...));
    }

    public function hydrateSupplier(array $suppliers): Collection
    {
        return Supplier::hydrate($suppliers);
    }

    public function joinSuppliers(Builder $query): Builder
    {
        return $query->join(
            'suppliers',
            'suppliers.id',
            '=',
            'stock_entries.supplier_id',
        )
            ->groupBy(['suppliers.name', 'suppliers.img', 'suppliers.color'])
            ->addSelect([
                'suppliers.name as supplierName',
                'suppliers.img as supplierImg',
                'suppliers.color as supplierColor',
            ]);
    }
}
