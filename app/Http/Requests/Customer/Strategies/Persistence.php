<?php

declare(strict_types=1);

namespace App\Http\Requests\Customer\Strategies;

use App\Http\Requests\Checker;
use App\Libraries\Enums\CustomerPhoneTypeEnum;
use App\Libraries\Values\PhoneValue;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

final class Persistence implements Checker
{
    protected int $nameMaxSize;

    protected int $emailMaxSize;

    protected int $hostessMaxSize;

    public function __construct(protected ?Customer $customer = null)
    {
        $this->nameMaxSize = \intval(
            config('database.schema.sizes.customer.name')
        );
        $this->emailMaxSize = \intval(
            config('database.schema.sizes.customer.email')
        );
        $this->hostessMaxSize = \intval(
            config('database.schema.sizes.customer.hostess')
        );
    }

    protected function makePhoneRules(): array
    {
        $phoneRule = [
            'bail',
            'nullable',
            PhoneValue::rule(),
        ];

        return collect([
            CustomerPhoneTypeEnum::CELULAR->value,
            CustomerPhoneTypeEnum::RESIDENTIAL->value,
            CustomerPhoneTypeEnum::COMMERCIAL->value,
        ])->reduce(function (array $acc, string $key) use (&$phoneRule) {
            $acc["phone.{$key}"] = $phoneRule;

            return $acc;
        }, []);
    }

    protected function makeCustomerUniqueRule(): Unique
    {
        /** @var User $user */
        $user = Auth::user();

        return when(
            $this->customer,
            fn (Customer $customer) => (
                Rule::unique('customers', 'email')->ignore($customer->id, 'id')
            ),
            Rule::unique('customers', 'email')
        )->where(fn (Builder $query) => (
            $query
                ->where('user_id', $user->id)
        ));
    }

    public function rules(): array
    {
        /** @var User $user */
        $user = Auth::user();

        return [
            'name' => "bail|required|min:2|max:{$this->nameMaxSize}",
            'email' => [
                'bail',
                'nullable',
                'email',
                "max:{$this->emailMaxSize}",

                $this->makeCustomerUniqueRule(),
            ],
            'hostess' => "bail|nullable|min:2|max:{$this->hostessMaxSize}",
            'birthdate' => [
                'bail',
                'nullable',
                'date',
                Rule::date()->format('Y-m-d'),
                Rule::date()->before(now()),
            ],
            ...$this->makePhoneRules(),
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Campo obrigatório',
            'name.min' => 'Tamanho mínimo (2)',
            'name.max' => "Tamanho máximo excedido ({$this->nameMaxSize})",

            'email.email' => 'Campo inválido',
            'email.max' => "Tamanho máximo excedido ({$this->emailMaxSize})",
            'email.unique' => 'E-mail já utilizado',

            'hostess.min' => 'Tamanho mínimo (2)',
            'hostess.max' => "Tamanho máximo excedido ({$this->hostessMaxSize})",

            'birthdate.date' => 'Insira uma data válida',
            'birthdate.date_format' => 'Formato de data inválida',
            'birthdate.before' => 'Data inválida',
        ];
    }
}
