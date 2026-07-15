<?php

declare(strict_types=1);

namespace App\Services;

use App\Libraries\Enums\CardPayWayEnum;
use App\Libraries\Enums\PaymentTypeEnum;
use App\Libraries\Traits\InputPickerTrait;
use App\Models\Customer;
use App\Models\Discount;
use App\Models\Payment;
use App\Models\PaymentCard;
use App\Models\Sale;
use App\Models\StockExit;
use App\Models\User;
use App\Services\Contracts\StockExitHandlerInterface;
use Carbon\CarbonInterface;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

final class StockExitSaleService implements StockExitHandlerInterface
{
    use InputPickerTrait;

    protected User $user;

    public function __construct()
    {
        $this->user = Auth::user();
    }

    /**
     * @return array{
     *      card?: array{
     *          payment_card_id: int,
     *          pay_way: CardPayWayEnum,
     *          fee_id?: int
     *      },
     * }
     */
    protected function getCardParams(Request $request, PaymentTypeEnum $payType): array
    {
        if ($payType !== PaymentTypeEnum::CARD) {
            return [];
        }

        return [
            'card' => $this->pickInputs(
                $request,
                [
                    'payment_card_id' => $request->input('card'),
                    'pay_way' => CardPayWayEnum::from($request->input('card_pay_way')),
                ],
                ['card_fee' => 'fee_id'],
            ),
        ];
    }

    protected function getCustomerId(Request $request): int|string
    {
        return $this->user->can(
            'viewAny',
            Customer::class
        ) ? $request->input('customer') : Customer::firstOrCreate(
            Customer::getAnonymousFields()
        )->id;
    }

    /**
     * @return array{
     *     discount_id?: int,
     *     user_id: int
     * }
     */
    protected function getSaleParams(Request $request): array
    {
        return [
            'sale' => $this->pickInputs(
                $request,
                [
                    'user_id' => $this->user->id,
                    'customer_id' => $this->getCustomerId($request),
                ],
                ['discount' => 'discount_id']
            ),
        ];
    }

    protected function sumTotalPrice(Request $request, Collection $productExits): float
    {
        $productIds = $productExits->keys();

        return collect($request->input('prices'))->reject(
            fn (string $price, int $productId) => ! $productIds->contains($productId)
        )->sum(function (string $price, int $productId) use (&$productExits) {
            $totalQty = $productExits->get($productId)->sum(
                fn (StockExit $exit) => \intval($exit->qty)
            );

            return \floatval($price) * $totalQty;
        });
    }

    /**
     * @param  Collection<int, Collection<StockExit>>  $productExits
     * @return array{
     *    payment: array{
     *        customer_id: int,
     *        type: PaymentTypeEnum,
     *        value: float,
     *    },
     * }
     */
    protected function getPaymentParams(Request $request, Collection $productExits, PaymentTypeEnum $payType): array
    {
        $totalPrice = $this->sumTotalPrice($request, $productExits);

        return [
            'payment' => [
                'value' => $totalPrice,
                'type' => $payType,
                'customer_id' => $this->getCustomerId($request),
            ],
        ];
    }

    /**
     * @return array{
     *      sale: array{
     *          discount_id?: int,
     *          user_id: int
     *      },
     *      payment: array{
     *          customer_id: int,
     *          type: PaymentTypeEnum,
     *          value: float,
     *      },
     *      card?: array{
     *          payment_card_id: int,
     *          pay_way: CardPayWayEnum,
     *          fee_id?: int
     *      },
     * }
     */
    protected function extractParams(Request $request, Collection $productExits): array
    {
        $payType = PaymentTypeEnum::from($request->input('payment-type'));

        return [
            ...$this->getSaleParams($request),
            ...$this->getPaymentParams($request, $productExits, $payType),
            ...$this->getCardParams($request, $payType),
        ];
    }

    /**
     * @param array{
     *     discount_id?: int,
     *     user_id: int
     * } $params
     */
    protected function makeSale(array $params): Sale
    {
        $args = collect($params);
        $discountId = $args->pull('discount_id');
        $sale = Sale::create($args->all());
        if ($discountId) {
            $sale->discount()->associate(
                Discount::find($discountId)
            );
            $sale->save();
        }

        return $sale;
    }

    /**
     * @param array{
     *      customer_id: int,
     *      type: PaymentTypeEnum,
     *      value: float,
     * } $params
     */
    protected function makePayment(Sale $sale, array $params): Payment
    {
        $args = collect($params);

        return Payment::create([
            'value' => $args->pull('value'),
            'type' => $args->pull('type'),
            'customer_id' => $args->pull('customer_id'),
            'sale_id' => $sale->id,
        ]);
    }

    /**
     * @param array{
     *     payment_card_id: int,
     *     pay_way: CardPayWayEnum,
     *     fee_id?: int
     * } $params
     */
    protected function bindCardPayment(Payment $payment, array $params): void
    {
        $args = collect($params);
        $paymentCard = PaymentCard::find($args->get('payment_card_id'));
        $pivotColumns = [
            'pay_way' => $args->get('pay_way'),
            'created_at' => $this->makeNow(),
            'updated_at' => $this->makeNow(),
        ];
        if ($args->has('fee_id')) {
            $fee = Discount::find($args->pull('fee_id'));
            $pivotColumns = ['fee_id' => $fee->id];
        }
        $payment->paymentCards()->save(
            $paymentCard,
            $pivotColumns
        );
    }

    /**
     * @param  Collection<int, Collection<StockExit>>  $productExits
     */
    protected function handleStockExits(Sale $sale, Collection $productExits): void
    {
        $productExits->each(function (Collection $exits) use (&$sale) {
            $exitList = $exits->mapWithKeys(fn (StockExit $exit) => [
                $exit->id => [
                    'created_at' => $this->makeNow(),
                    'updated_at' => $this->makeNow(),
                ],
            ]);
            $sale->stockExits()->attach($exitList);
        });
    }

    protected function makeNow(): CarbonInterface
    {
        return now(new DateTimeZone('America/Sao_Paulo'));
    }

    /**
     * @param  Collection<int, Collection<StockExit>>  $productExits
     */
    public function handle(Request $request, Collection $productExits): void
    {
        $saleParams = collect($this->extractParams($request, $productExits));
        $sale = $this->makeSale($saleParams->get('sale'));
        $payment = $this->makePayment($sale, $saleParams->get('payment'));
        if ($payment->type === PaymentTypeEnum::CARD) {
            $this->bindCardPayment($payment, $saleParams->get('card'));
        }
        $this->handleStockExits($sale, $productExits);
    }
}
