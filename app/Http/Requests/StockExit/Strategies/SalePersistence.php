<?php

declare(strict_types=1);

namespace App\Http\Requests\StockExit\Strategies;

use App\Http\Requests\Checker;
use App\Libraries\Enums\CardPayWayEnum;
use App\Libraries\Enums\PaymentTypeEnum;
use App\Models\Customer;
use App\Models\Discount;
use App\Models\PaymentCard;
use App\Models\User;
use App\Rules\StockEntriesToExitRule;
use App\Rules\StockSalePricesToExitRule;
use App\Services\StockEntryService;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

final class SalePersistence implements Checker
{
    protected User $user;

    protected int $valueMinSize;

    protected int $valueMaxSize;

    protected StockEntryService $entrySvc;

    public function __construct()
    {
        $this->user = Auth::user();
        $this->valueMinSize = \intval(
            config('database.schema.sizes.generic.decimal.min')
        );
        $this->valueMaxSize = \intval(
            config('database.schema.sizes.generic.decimal.max')
        );
        $this->entrySvc = app(StockEntryService::class);
    }

    protected function getCustomerRules(): array
    {
        if ($this->user->cannot('viewAny', Customer::class)) {
            return [];
        }

        return [
            'customer' => [
                'required',
                Rule::exists('customers', 'id')->where(function (Builder $query) {
                    $query->where('user_id', '=', $this->user->id);
                }),
            ],
        ];
    }

    protected function getPaymentCardRules(): array
    {
        if ($this->user->cannot('viewAny', PaymentCard::class)) {
            return [];
        }

        return [
            'card' => [
                'bail',
                'nullable',
                Rule::exists('payment_cards', 'id')->where(function (Builder $query) {
                    $query
                        ->where('user_id', '=', $this->user->id)
                        ->orWhere('native', '=', 1);
                }),
            ],
            'card_pay_way' => [
                'bail',
                'nullable',
                Rule::enum(CardPayWayEnum::class),
            ],
        ];
    }

    protected function getSaleDiscountRules(): array
    {
        return [
            'discount' => [
                'bail',
                'nullable',
                Rule::exists('discounts', 'id')->where(function (Builder $query) {
                    $query
                        ->where('user_id', '=', $this->user->id)
                        ->orWhere('native', '=', 1);
                }),
            ],
        ];
    }

    protected function getAllDiscountRules(): array
    {
        if ($this->user->cannot('viewAny', Discount::class)) {
            return [];
        }
        if ($this->user->cannot('viewAny', PaymentCard::class)) {
            return $this->getSaleDiscountRules();
        }

        return [
            'card_fee' => [
                'bail',
                'nullable',
                Rule::exists('discounts', 'id')->where(function (Builder $query) {
                    $query
                        ->where('user_id', '=', $this->user->id)
                        ->orWhere('native', '=', 1);
                }),
            ],
            ...$this->getSaleDiscountRules(),
        ];
    }

    public function rules(): array
    {
        return [
            ...$this->getCustomerRules(),

            'payment-type' => [
                'required',
                when(
                    $this->user->can('viewAny', PaymentCard::class),
                    Rule::in(
                        array_column(PaymentTypeEnum::cases(), 'value')
                    ),
                    Rule::in(
                        array_column(
                            PaymentTypeEnum::casesExcept(PaymentTypeEnum::CARD),
                            'value'
                        )
                    )
                ),
            ],

            ...$this->getPaymentCardRules(),

            ...$this->getAllDiscountRules(),

            'prices' => [
                'bail',
                'array',
                new StockSalePricesToExitRule(),
            ],
            'entries' => [
                'bail',
                'array',
                new StockEntriesToExitRule(),
            ],
        ];
    }

    protected function getCustomerMessages(): array
    {
        if ($this->user->cannot('viewAny', Customer::class)) {
            return [];
        }

        return [
            'customer.required' => 'Campo obrigatório',
            'customer.exists' => 'Cliente inválido',
        ];
    }

    protected function getPaymentCardMessages(): array
    {
        if ($this->user->cannot('viewAny', PaymentCard::class)) {
            return [];
        }

        return [
            'card.exists' => 'Requisição inválida',
            'card_pay_way.enum' => 'Forma de pagamento inválida',
        ];
    }

    protected function getSaleDiscountMessages(): array
    {
        return [
            'discounts.exists' => 'Desconto inválido',
        ];
    }

    protected function getAllDiscountMessages(): array
    {
        if ($this->user->cannot('viewAny', Discount::class)) {
            return [];
        }
        if ($this->user->cannot('viewAny', PaymentCard::class)) {
            return $this->getSaleDiscountMessages();
        }

        return [
            'card_fee.exists' => 'Desconto de cartão inválido',
            ...$this->getSaleDiscountMessages(),
        ];
    }

    public function messages(): array
    {
        return [
            ...$this->getCustomerMessages(),

            'payment-type.required' => 'Campo obrigatório',
            'payment-type.in' => 'Pagamento inválido',

            ...$this->getPaymentCardMessages(),

            ...$this->getAllDiscountMessages(),

            'prices.array' => 'Preços inválidos',
            'entries.array' => 'Estoque inválido',
        ];
    }
}
