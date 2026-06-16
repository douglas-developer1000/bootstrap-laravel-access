<?php

declare(strict_types=1);

namespace App\Services;

use App\Libraries\Enums\DiscountTypeEnum;
use App\Libraries\Traits\InputPickerTrait;
use App\Libraries\Traits\OneOrManyMsgTrait;
use App\Libraries\Utils\DatetimeFormatter;
use App\Models\Product;
use App\Models\StockEntry;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Number;
use Illuminate\Support\Collection;
use Closure;

final class StockEntryService
{
    use InputPickerTrait, OneOrManyMsgTrait;

    protected int|string $userId;

    /**
     * Create a new class instance.
     */
    public function __construct(
        protected ProductService $prodSvc,
        protected SupplierService $supplierSvc,
    ) {
        $this->userId = Auth::id();
    }

    public function extractStockEntryParams(Request $request, Product $product)
    {
        return $this->pickInputs(
            $request,
            [
                'cost' => $request->input('cost'),
                'qty' => $request->input('qty'),
                'supplier_id' => $request->input('supplier'),
                'product_id' => $product->id,
                'user_id' => $this->userId,
                'discount_id' => $request->input('discount'),
            ],
            'validity',
        );
    }

    public function createStockEntry(array $params): StockEntry
    {
        return StockEntry::create($params);
    }

    public function getRemainStockEntries(Product $product)
    {
        return $this->joinStockEntries(
            query: $this->prodSvc->queryProduct(
                deleted: false,
                alias: 'p',
                callback: fn($query) => $query->where(
                    ['p.user_id' => $this->userId]
                )
            ),
            productTableName: 'p',
            callback: fn(Builder $query) => $query->addSelect([
                'stock_entries.id as stockEntryId',
                'stock_entries.validity',
                'stock_entries.created_at',
            ])
                ->orderBy(
                    'stock_entries.validity',
                    'asc'
                )
                ->orderBy(
                    'stock_entries.created_at',
                    'asc'
                )
                ->groupBy([
                    'stockEntryId',
                    'stock_entries.validity',
                    'stock_entries.created_at',
                ])
        )->where(
            ['p.id' => $product->id]
        )
            ->get([
                'sub.stockEntryId as id',
                'sub.validity',
                $this->columnNullZerable('sub.remain', 'qtyRemain'),
            ])->map(function ($entry) {
                $entry->validity = DatetimeFormatter::formatToDate($entry->validity) ?? 'N/A';
                $entry->sizeView = $this->makeSizeMsg(\intval($entry->qtyRemain), 'item', 'items');
                return $entry;
            });
    }

    public function joinStockEntriesQtySummarized(Builder $query, string $productTableName = 'products')
    {
        $queryFromQties = DB::table('stock_entries')
            ->select(
                'stock_entries.product_id',
                DB::raw('SUM(stock_entries.qty) as available'),
                'tb_uses.uses as uses',
            )
            ->groupBy('stock_entries.product_id')
            ->groupBy('uses')
            ->joinSub(
                DB::table('stock_entries')
                    ->leftJoin(
                        'stock_exits',
                        'stock_entries.id',
                        '=',
                        'stock_exits.stock_entry_id'
                    )
                    ->select([
                        'stock_entries.product_id',
                        DB::raw('COALESCE(SUM(stock_exits.qty), 0) as uses'),
                    ])
                    ->groupBy('stock_entries.product_id')
                    ->where('stock_entries.user_id', '=', $this->userId),
                'tb_uses',
                function ($join) {
                    $join->on('stock_entries.product_id', '=', 'tb_uses.product_id');
                }
            )
            ->where('stock_entries.user_id', '=', $this->userId);

        return $query
            ->addSelect([
                DB::raw('(COALESCE(sub.available - sub.uses, 0)) as qtyRemain'),
            ])
            ->leftJoinSub(
                $queryFromQties,
                'sub',
                function ($join) use ($productTableName) {
                    $join->on("{$productTableName}.id", '=', 'sub.product_id');
                }
            )
            ->groupBy('sub.available')
            ->groupBy('sub.uses');
    }

    /**
     * @param Closure(Builder): Builder $callback
     */
    public function joinStockEntries(Builder $query, string $productTableName = 'products', ?Closure $callback = NULL): Builder
    {
        $subQuery = $this->makeAvailableStockEntriesQuery()->when(
            \is_callable($callback),
            $callback(...)
        );
        return $query->leftJoinSub(
            $subQuery,
            'sub',
            function ($join) use ($productTableName) {
                $join->on("{$productTableName}.id", '=', 'sub.product_id');
            }
        );
    }

    /**
     * Search by stock_entries database tuples with qty > 0.
     * The available columns for each tuple are:
     *
     * - 'product_id'
     * - 'stock_entries.qty'
     * - 'remain'
     *
     */
    protected function makeAvailableStockEntriesQuery(): Builder
    {
        return DB::table('stock_entries')->leftJoin(
            'stock_exits',
            'stock_entries.id',
            '=',
            'stock_exits.stock_entry_id'
        )->select([
            'stock_entries.product_id',
            'stock_entries.qty',
            DB::raw('(stock_entries.qty - COALESCE(SUM(stock_exits.qty), 0)) as remain'),
        ])
            ->groupBy('stock_entries.product_id')
            ->groupBy('stock_entries.qty')
            ->havingRaw('remain > 0')

            ->where('stock_entries.user_id', '=', $this->userId);
    }

    /**
     * @return array{emptyStock: bool, entries: Collection<int, mixed>}
     */
    public function getProductStockEntries(Product $product, bool $deleted = false)
    {
        $entries = $this->joinStockEntries(
            query: $this->prodSvc->queryProduct(
                deleted: $deleted,
                alias: 'p',
                callback: fn($query) => $query->where(
                    ['p.user_id' => $this->userId]
                )
            ),
            productTableName: 'p',
            callback: fn(Builder $query) => $this->supplierSvc->joinSuppliers($query)
                ->addSelect([
                    'stock_entries.id as stockEntryId',
                    'stock_entries.cost',
                    'stock_entries.validity',
                    'stock_entries.created_at',

                    'discounts.type as discountType',
                    'discounts.value as discountValue',
                ])
                ->leftJoin(
                    'discounts',
                    'stock_entries.discount_id',
                    '=',
                    'discounts.id'
                )
                ->groupBy([
                    'stockEntryId',
                    'stock_entries.cost',
                    'stock_entries.validity',
                    'stock_entries.created_at',

                    'discountType',
                    'discountValue',
                ])
        )->where(
            ['p.id' => $product->id]
        )->get([
            'sub.stockEntryId',
            'sub.cost',
            'sub.validity',
            'sub.created_at',
            $this->columnNullZerable('sub.remain', 'qtyRemain'),
            'sub.supplierName',
            'sub.supplierImg',
            'sub.supplierColor',

            'sub.discountType',
            'sub.discountValue',
        ]);

        return [
            'emptyStock' => $entries->count() === 1 && $entries->first()->qtyRemain === '0',
            'entries' => $entries->map(function ($item) {
                $subject = ngettext('item', 'itens', \intval($item->qtyRemain));
                $item->qtyRemain = "{$item->qtyRemain} {$subject}";
                $item->validity = DatetimeFormatter::formatToDate($item->validity) ?? 'N/A';
                $item->created_at = DatetimeFormatter::formatToDate($item->created_at);
                $item->cost = $this->parseCurrencyValue(\floatval($item->cost));
                $item->discountValue = $this->parseDiscount($item->discountType, $item->discountValue);
                return $item;
            })
        ];
    }

    protected function parseDiscount(string|null $type, string|null $value): string
    {
        if ($type === NULL) {
            return 'N/A';
        }
        return DiscountTypeEnum::parseDiscountValue(
            type: $type,
            value: \floatval($value ?? 0)
        );
    }

    protected function parseCurrencyValue(float|int $value): string
    {
        return Number::currency(
            number: $value,
            in: 'BRL',
            locale: 'pt_BR',
            precision: 2
        );
    }

    protected function columnNullZerable(string $column, ?string $alias = NULL)
    {
        $statement = "CASE WHEN {$column} IS NULL THEN 0 ELSE {$column} END";
        if ($alias !== NULL) {
            return DB::raw(\sprintf("($statement) AS {$alias}"));
        }
        return DB::raw($statement);
    }
}
