<?php

declare(strict_types=1);

namespace App\Services;

use App\Libraries\Enums\CardPayWayEnum;
use App\Libraries\Traits\PicRequestHandleTrait;
use App\Models\PaymentCard;
use App\Models\User;
use App\Services\Abstracts\AbstractPaginatorIndex;
use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Override;

final class PaymentCardService
{
    use PicRequestHandleTrait;
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
                $trashed = $request->boolean('trashed');
                $deletedColumn = (new PaymentCard())->getDeletedAtColumn();

                return $this->filterCardsOwnership(
                    $request,
                    $deletedColumn,
                    $trashed
                );
            }

            protected function filterCardsOwnership(
                Request $request,
                string $deletedColumn,
                bool $trashed
            ): Builder {
                return $this->buildNonNativeQuery(
                    $deletedColumn,
                    $trashed
                )
                    ->when(
                        !$request->boolean('own'),
                        fn(Builder $query) => $query->union(
                            $this->buildNativeQuery(
                                $deletedColumn,
                                $trashed
                            )
                        )
                    );
            }

            protected function buildNonNativeQuery(string $deletedColumn, bool $trashed): Builder
            {
                return PaymentCard::whereBelongsTo($this->user)->getQuery()
                    ->when(
                        $trashed,
                        fn(Builder $builder) => $builder->whereNotNull($deletedColumn)
                    )
                    ->when(
                        !$trashed,
                        fn(Builder $builder) => $builder->whereNull($deletedColumn)
                    );
            }

            protected function buildNativeQuery(string $deletedColumn, bool $trashed): Builder
            {
                return PaymentCard::where([
                    'native' => 1
                ])->getQuery()
                    ->when(
                        $trashed,
                        fn(Builder $query) => $query->whereNotNull($deletedColumn)
                    )
                    ->when(
                        !$trashed,
                        fn(Builder $query) => $query->whereNull($deletedColumn)
                    );
            }

            #[Override]
            public function getSortColumns(): array
            {
                return ['created_at', 'flag'];
            }
        })->prepareIndex(
            $request,
            '*'
        );
    }

    public function createPaymentCard(array $params): PaymentCard
    {
        return PaymentCard::create($params);
    }

    public function updatePaymentCard(array $params, PaymentCard $card): void
    {
        $card->update($params);
    }

    /**
     * @param PaymentCard[] $cards
     */
    public function removePaymentCardGroup(array $cards): void
    {
        collect($cards)->each($this->removePaymentCard(...));
    }

    public function removePaymentCard(PaymentCard $card): void
    {
        $relationQty = $card->paymentPaymentCard()->count('id');
        if ($relationQty > 0) {
            $card->delete();
        } else {
            $card->forceDelete();
        }
    }

    public function extractPaymentCardParams(Request $request, ?PaymentCard $card = NULL): array
    {
        return $this->attachImgInput(
            [
                'flag' => $request->input('flag'),
                'pay_way' => CardPayWayEnum::wrapRequestBooleanEnum(
                    $request,
                    'pay_way'
                ),
                'native' => $request->input('native'),
                'user_id' => $this->user->id,
            ],
            $request,
            \strval($this->user->id),
            'img',
            $card
        );
    }

    public function restorePaymentCard(PaymentCard $card)
    {
        $card->restore();
    }

    public function restorePaymentCardGroup(array $cards): void
    {
        collect($cards)->each($this->restorePaymentCard(...));
    }

    public function hydratePaymentCard(array $paymentCards): Collection
    {
        return PaymentCard::hydrate($paymentCards);
    }

    public function getPaymentCards()
    {
        return PaymentCard::all();
    }
}
