<?php

declare(strict_types=1);

namespace App\Http\Requests\Customer\Strategies;

use App\Http\Requests\Checker;
use App\Rules\PhoneValid;
use Illuminate\Validation\Rule;
use App\Libraries\Enums\CustomerPhoneTypeEnum;

class Persistence implements Checker
{
    protected int $nameMaxSize;
    protected int $emailMaxSize;
    protected int $hostessMaxSize;
    protected int $phoneMaxSize;
    protected int $phoneMinSize;

    public function __construct()
    {
        $this->nameMaxSize = \intval(
            config('database.schema.sizes.client.name')
        );
        $this->emailMaxSize = \intval(
            config('database.schema.sizes.client.email')
        );
        $this->hostessMaxSize = \intval(
            config('database.schema.sizes.client.hostess')
        );
        $this->phoneMinSize = 9;
        $this->phoneMaxSize = \intval(
            config('database.schema.sizes.client.phone')
        );
    }

    protected function makePhoneRules(): array
    {
        $phoneRule = [
            'bail',
            'nullable',
            "min:{$this->phoneMinSize}",
            new PhoneValid($this->phoneMaxSize)
        ];
        return collect([
            CustomerPhoneTypeEnum::CELULAR->value,
            CustomerPhoneTypeEnum::RESIDENTIAL->value,
            CustomerPhoneTypeEnum::COMMERCIAL->value
        ])->reduce(function (array $acc, string $key) use (&$phoneRule) {
            $acc["phone.{$key}"] = $phoneRule;
            return $acc;
        }, []);
    }

    protected function makePhoneMessages(): array
    {
        $phoneMinSize = $this->phoneMinSize;
        return collect([
            CustomerPhoneTypeEnum::CELULAR->value,
            CustomerPhoneTypeEnum::RESIDENTIAL->value,
            CustomerPhoneTypeEnum::COMMERCIAL->value
        ])->reduce(function (array $acc, string $key) use ($phoneMinSize) {
            $acc["phone.{$key}.min"] = "Tamanho mínimo ({$phoneMinSize})";
            return $acc;
        }, []);
    }

    public function rules(): array
    {
        return [
            'name' => "bail|required|min:2|max:{$this->nameMaxSize}",
            'email' => "bail|required|email|max:{$this->emailMaxSize}|unique:App\Models\Customer,email",
            'hostess' => "bail|nullable|min:2|max:{$this->hostessMaxSize}",
            'birthdate' => [
                'bail',
                'nullable',
                'date',
                Rule::date()->format('Y-m-d'),
                Rule::date()->before(now())
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

            'email.required' => 'Campo obrigatório',
            'email.email' => 'Campo inválido',
            'email.max' => "Tamanho máximo excedido ({$this->emailMaxSize})",
            'email.unique' => 'E-mail já utilizado',

            'hostess.min' => 'Tamanho mínimo (2)',
            'hostess.max' => "Tamanho máximo excedido ({$this->hostessMaxSize})",

            'birthdate.date' => 'Insira uma data válida',
            'birthdate.date_format' => 'Formato de data inválida',
            'birthdate.before' => 'Data inválida',

            ...$this->makePhoneMessages(),
        ];
    }
}
