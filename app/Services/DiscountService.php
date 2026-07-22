<?php

declare(strict_types=1);

namespace App\Services;

use App\Libraries\Traits\InputPickerTrait;
use App\Models\Discount;
use App\Models\User;
use App\Services\Abstracts\AbstractPaginatorIndex;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Override;

final class DiscountService
{
    use InputPickerTrait;

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
                $trashed = $request->boolean('trashed');
                $deletedColumn = (new Discount())->getDeletedAtColumn();

                return $this->filterDiscountsOwnership(
                    $request,
                    $deletedColumn,
                    $trashed
                );
            }

            protected function filterDiscountsOwnership(
                Request $request,
                string $deletedColumn,
                bool $trashed
            ): Builder {
                return $this->buildNonNativeQuery(
                    $deletedColumn,
                    $trashed
                )
                    ->when(
                        ! $request->boolean('own'),
                        fn (Builder $query) => $query->union(
                            $this->buildNativeQuery(
                                $deletedColumn,
                                $trashed
                            )
                        )
                    );
            }

            protected function buildNonNativeQuery(string $deletedColumn, bool $trashed): Builder
            {
                return Discount::whereBelongsTo($this->user)->getQuery()
                    ->when(
                        $trashed,
                        fn (Builder $query) => $query->whereNotNull($deletedColumn)
                    )
                    ->when(
                        ! $trashed,
                        fn (Builder $query) => $query->whereNull($deletedColumn)
                    );
            }

            protected function buildNativeQuery(string $deletedColumn, bool $trashed): Builder
            {
                return Discount::where([
                    'native' => 1,
                ])->getQuery()
                    ->when(
                        $trashed,
                        fn (Builder $query) => $query->whereNotNull($deletedColumn)
                    )
                    ->when(
                        ! $trashed,
                        fn (Builder $query) => $query->whereNull($deletedColumn)
                    );
            }

            #[Override]
            public function getSortColumns(): array
            {
                return ['created_at', 'type', 'value'];
            }
        })->prepareIndex(
            $request,
            '*'
        );
    }

    public function getAllDiscounts()
    {
        return Discount::whereBelongsTo($this->user)->orWhere([
            'native' => 1,
        ])->get(['id', 'type', 'value']);
    }

    public function extractDiscountParams(Request $request): array
    {
        return [
            ...$request->only(['type', 'value']),
            'user_id' => $this->user->id,
        ];
    }

    public function createDiscount(array $params): Discount
    {
        return Discount::create($params);
    }

    public function updateDiscount(array $params, Discount $discount): void
    {
        $discount->update($params);
    }

    public function removeDiscount(Discount $discount): void
    {
        if (
            $discount->stockEntries()->exists() ||
            $discount->sales()->exists() ||
            $discount->paymentCards()->exists()
        ) {
            $discount->delete();
        } else {
            $discount->forceDelete();
        }
    }

    /**
     * @param  Discount[]  $discounts
     */
    public function removeDiscountList(array $discounts): void
    {
        collect($discounts)->each($this->removeDiscount(...));
    }

    public function restoreDiscount(Discount $discount): void
    {
        $discount->restore();
    }

    public function restoreGroup(array $idList): void
    {
        DB::table('discounts')
            ->whereNotNull('deleted_at')
            ->whereIn('id', $idList)
            ->update(['deleted_at' => null]);
    }

    public function hydrateDiscount(array $discounts): Collection
    {
        return Discount::hydrate($discounts);
    }
}
