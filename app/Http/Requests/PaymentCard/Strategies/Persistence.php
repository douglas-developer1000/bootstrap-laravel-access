<?php

declare(strict_types=1);

namespace App\Http\Requests\PaymentCard\Strategies;

use App\Http\Requests\Checker;
use App\Libraries\Enums\CardPayWayEnum;
use App\Libraries\Traits\ImgCheckerTrait;
use App\Libraries\Traits\NativeCheckerTrait;
use App\Libraries\Traits\OneOrManyMsgTrait;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use Closure;

class Persistence implements Checker
{
    use NativeCheckerTrait, ImgCheckerTrait, OneOrManyMsgTrait;
    protected int $flagMinSize;
    protected int $flagMaxSize;
    protected Collection $paywayValues;
    public function __construct()
    {
        $this->flagMinSize = \intval(
            config('database.schema.sizes.payment-card.flag.min')
        );
        $this->flagMaxSize = \intval(
            config('database.schema.sizes.payment-card.flag.max')
        );
        $this->paywayValues = collect(
            array_column(CardPayWayEnum::cases(), 'value')
        );
        $this->loadImgProps();
    }

    public function rules(): array
    {
        return [
            ...$this->pickImgRules(),

            'flag' => [
                'required',
                "min:{$this->flagMinSize}",
                "max:{$this->flagMaxSize}",
                Rule::unique('payment_cards', 'flag'),
            ],
            'pay_way' => [
                'required',
                'array',
                Rule::anyOf(
                    $this->paywayValues->map(
                        fn($val) => ["array:{$val}"]
                    )->all()
                )
            ],
            'pay_way.*' => function (string $attribute, mixed $value, Closure $fail) {
                $key = Str::of($attribute)->after('pay_way.')->toString();
                if (!$this->paywayValues->contains($key)) {
                    $fail('Escolha pagamentos válidos');
                }
            },
            ...$this->pickNativeRules(),
        ];
    }

    public function messages(): array
    {
        $flagMinMsg = $this->makeSizeMsg(
            $this->flagMinSize,
            'caracter',
            'caracteres'
        );
        $flagMaxMsg = $this->makeSizeMsg(
            $this->flagMaxSize,
            'caracter',
            'caracteres'
        );

        return [
            'flag.required' => 'Campo obrigatório',
            'flag.min' => "Tamanho mínimo: {$flagMinMsg}",
            'flag.max' => "Tamanho máximo excedido: {$flagMaxMsg}",
            'flag.unique' => 'Bandeira já utilizada',

            'pay_way.required' => 'Escolha um pagamento',
            'pay_way.array' => 'Requisição inválida',
            'pay_way.any_of' => 'Escolha pagamentos válidos',

            ...$this->pickImgMessages(),
            ...$this->pickNativeMessages(),
        ];
    }
}
