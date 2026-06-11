<?php

declare(strict_types=1);

namespace App\Services;

use App\Libraries\Enums\StockExitTypeEnum;
use App\Models\StockExit;
use App\Models\User;
use App\Services\Abstracts\AbstractPaginatorIndex;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Override;

final class RawExitService
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
            public function __construct(
                protected User $user,
            ) {
                parent::__construct();
            }

            #[Override]
            public function query(Request $request): Builder
            {
                return StockExit::whereBelongsTo($this->user)
                    ->join(
                        'stock_entries',
                        'stock_entries.id',
                        '=',
                        'stock_exits.stock_entry_id'
                    )
                    ->join(
                        'products',
                        'products.id',
                        '=',
                        'stock_entries.product_id'
                    )
                    ->select([
                        'products.name as product',

                        'stock_exits.id',
                        'stock_exits.type',
                        'stock_exits.created_at',
                        'stock_exits.user_id',
                    ])
                    ->where('type', '=', StockExitTypeEnum::RAW->value)
                    ->getQuery();
            }

            #[Override]
            public function getSortColumns(): array
            {
                return ['created_at', 'type', 'product'];
            }
        })->prepareIndex(
            $request,
            '*'
        );
    }

    public function hydrateStockExit(array $exits): Collection
    {
        return StockExit::hydrate($exits)->map(
            function (StockExit $exit, int $i) use ($exits) {
                $exit->product = $exits[$i]->product;
                return $exit;
            }
        );
    }
}
