<?php

declare(strict_types=1);

namespace App\Http\Requests\StockExit\Strategies;

use App\Http\Requests\Checker;
use App\Libraries\Enums\CardPayWayEnum;
use App\Libraries\Enums\PaymentTypeEnum;
use App\Rules\StockSalePricesToExitRule;
use App\Rules\StockEntriesToExitRule;
use App\Services\StockEntryService;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

final class SalePersistence implements Checker
{
    protected string|int $userId;
    protected int $valueMinSize;
    protected int $valueMaxSize;
    protected StockEntryService $entrySvc;

    public function __construct()
    {
        $this->userId = Auth::id();
        $this->valueMinSize = \intval(
            config('database.schema.sizes.generic.decimal.min')
        );
        $this->valueMaxSize = \intval(
            config('database.schema.sizes.generic.decimal.max')
        );
        $this->entrySvc = app(StockEntryService::class);
    }
    public function rules(): array
    {
        return [
            'customer' => [
                'required',
                Rule::exists('customers', 'id')->where(function (Builder $query) {
                    $query->where('user_id', '=', $this->userId);
                })
            ],
            'payment-type' => [
                'required',
                Rule::enum(PaymentTypeEnum::class)
            ],
            'card' => [
                'bail',
                'nullable',
                Rule::exists('payment_cards', 'id')->where(function (Builder $query) {
                    $query
                        ->where('user_id', '=', $this->userId)
                        ->orWhere('native', '=', 1);
                })
            ],
            'card_pay_way' => [
                'bail',
                'nullable',
                Rule::enum(CardPayWayEnum::class)
            ],
            'card_fee' => [
                'bail',
                'nullable',
                Rule::exists('discounts', 'id')->where(function (Builder $query) {
                    $query
                        ->where('user_id', '=', $this->userId)
                        ->orWhere('native', '=', 1);
                })
            ],
            'discount' => [
                'bail',
                'nullable',
                Rule::exists('discounts', 'id')->where(function (Builder $query) {
                    $query
                        ->where('user_id', '=', $this->userId)
                        ->orWhere('native', '=', 1);
                })
            ],
            'prices' => [
                'bail',
                'array',
                new StockSalePricesToExitRule()
            ],
            'entries' => [
                'bail',
                'array',
                new StockEntriesToExitRule()
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'customer.required' => 'Campo obrigatório',
            'customer.exists' => 'Cliente obrigatório',

            'payment-type.required' => 'Campo obrigatório',
            'payment-type.enum' => 'Requisição inválida',

            'card.exists' => 'Requisição inválida',

            'card_pay_way.enum' => 'Forma de pagamento inválida',

            'discounts.exists' => 'Desconto inválido',

            'prices.array' => 'Preços inválidos',
            'entries.array' => 'Estoque inválido',
        ];
    }
}
