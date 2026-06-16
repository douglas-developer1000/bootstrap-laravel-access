<?php

declare(strict_types=1);

namespace App\Http\Requests\StockExit\Strategies;

use App\Http\Requests\BeforeValidationInterface;
use App\Http\Requests\Checker;
use App\Libraries\Traits\OneOrManyMsgTrait;
use App\Rules\StockEntriesToExitRule;

final class ExchangePersistence implements Checker
{
    use OneOrManyMsgTrait;
    protected int $personMinSize;
    protected int $personMaxSize;
    public function __construct(BeforeValidationInterface $before)
    {
        $this->personMinSize = \intval(
            config('database.schema.sizes.exchange-exit.person.min')
        );
        $this->personMaxSize = \intval(
            config('database.schema.sizes.exchange-exit.person.max')
        );
        $before->pushBeforeValidation(fn($formRequest) => when(
            $formRequest->input('person'),
            fn(string $person) => $formRequest->merge([
                'person' => trim($person)
            ])
        ));
    }
    public function rules(): array
    {
        return [
            'person' => [
                'required',
                "min:{$this->personMinSize}",
                "max:{$this->personMaxSize}",
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
        $msgs = collect([
            'personMinName' => $this->personMinSize,
            'personMaxName' => $this->personMaxSize,
        ])->map(
            fn(int $qty) => $this->makeSizeMsg($qty, 'caracter', 'caracteres')
        )->all();

        return [
            'person.required' => 'Campo obrigatório',
            'person.min' => "Tamanho mínimo: {$msgs['personMinName']}",
            'person.max' => "Tamanho máximo excedido: {$msgs['personMaxName']}",

            'entries.array' => 'Estoque inválido',
        ];
    }
}
