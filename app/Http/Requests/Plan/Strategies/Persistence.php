<?php

declare(strict_types=1);

namespace App\Http\Requests\Plan\Strategies;

use App\Facades\ListStorager;
use App\Http\Requests\BeforeValidationInterface;
use App\Http\Requests\Checker;
use App\Http\Requests\LateValidationInterface;
use App\Libraries\Enums\BillingPeriodEnum;
use App\Libraries\Traits\OneOrManyMsgTrait;
use App\Models\Plan;
use App\Services\PlanService;
use Closure;
use Exception;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

final class Persistence implements Checker
{
    use OneOrManyMsgTrait;

    protected int $nameMinSize;

    protected int $nameMaxSize;

    protected float $priceMinValue;

    protected float $priceMaxValue;

    protected int $descMaxSize;

    public function __construct(protected FormRequest $formRequest, protected ?Plan $plan = null)
    {
        $this->nameMinSize = \intval(
            config('database.schema.sizes.plan.name.min')
        );
        $this->nameMaxSize = \intval(
            config('database.schema.sizes.plan.name.max')
        );
        $this->priceMinValue = \floatval(
            config('database.schema.sizes.generic.decimal.min')
        );
        $this->priceMaxValue = \floatval(
            config('database.schema.sizes.generic.decimal.max')
        );
        $this->descMaxSize = \intval(
            config('database.schema.sizes.generic.obs.max')
        );
    }

    public function applyAfterValidation(LateValidationInterface $late): self
    {
        if (! $this->plan) {
            throw new Exception('Plan attribute required!');
        }
        $planRoles = $this->plan->roles->pluck('name')->all();
        $list = collect(ListStorager::getList('rolesToPlan'))->diff(
            $planRoles
        );
        $late->pushAfterValidation(
            function (Validator $validator) use (&$list, &$late) {
                $inputRoles = collect($late->getInput('roles'));
                if ($inputRoles->isEmpty() && $list->isEmpty()) {
                    $validator->errors()->add('roles', 'Plano sem papéis');
                }
            }
        );

        return $this;
    }

    public function trimFields(BeforeValidationInterface $before): self
    {
        $before->pushBeforeValidation(function ($formRequest) {
            $parsedInputs = [
                'name' => trim($formRequest->input('name', '')),
            ];
            if ($formRequest->has('description')) {
                $parsedInputs['description'] = trim(
                    $formRequest->input('description', '')
                );
            }
            $formRequest->merge($parsedInputs);
        });

        return $this;
    }

    public function validateRolesToPlan(BeforeValidationInterface $before): self
    {
        $list = collect(ListStorager::getList('rolesToPlan'));
        $before->pushBeforeValidation(function ($formRequest) use (&$list) {
            if ($list->isEmpty()) {
                abort(403);
            }
        });

        return $this;
    }

    protected function pullRolesValidation(): array
    {
        if ($this->plan !== null) {
            return [
                'roles' => [
                    'nullable',
                    'bail',
                    'array',
                ],
                'roles.*' => [
                    Rule::in($this->plan->roles->pluck('name')->all()),
                ],
            ];
        }

        return [];
    }

    public function rules(): array
    {
        return [
            'billing_period' => [
                'bail',
                'required',
                Rule::enum(BillingPeriodEnum::class),
            ],
            'name' => [
                'bail',
                'required',
                "min:{$this->nameMinSize}",
                "max:{$this->nameMaxSize}",
                function (string $attribute, mixed $value, Closure $fail) {
                    $slug = app(PlanService::class)->mountSlug(
                        $value,
                        $this->formRequest->input('billing_period')
                    );
                    if (
                        Plan::whereSlug($slug)->getQuery()
                            ->when(
                                $this->plan,
                                fn (Builder $query, Plan $plan) => $query->whereNot('id', $plan->id)
                            )
                            ->exists()
                    ) {
                        $fail('Nome já utilizado');
                    }
                },
            ],
            'price' => [
                'bail',
                'required',
                'decimal:0,2',
                "min:{$this->priceMinValue}",
                "max:{$this->priceMaxValue}",
            ],
            'description' => [
                'nullable',
                'bail',
                "max:{$this->descMaxSize}",
            ],
            'additionals' => [
                'nullable',
                'bail',
                'array',
            ],
            'additionals.*' => [
                'bail',
                'integer',
                Rule::exists('roles', 'id'),
            ],
            ...$this->pullRolesValidation(),
        ];
    }

    public function messages(): array
    {
        $nameMinMsg = $this->makeSizeMsg(
            $this->nameMinSize,
            'caracter',
            'caracteres'
        );
        $nameMaxMsg = $this->makeSizeMsg(
            $this->nameMaxSize,
            'caracter',
            'caracteres'
        );
        $descMaxMsg = $this->makeSizeMsg(
            $this->descMaxSize,
            'caracter',
            'caracteres'
        );

        return [
            'name.required' => 'Campo obrigatório',
            'name.min' => "Tamanho mínimo: {$nameMinMsg}",
            'name.max' => "Tamanho máximo excedido: {$nameMaxMsg}",

            'billing_period.required' => 'Campo obrigatório',
            'billing_period.in' => 'Faturamento inválido',

            'price.required' => 'Campo obrigatório',
            'price.decimal' => 'Campo inválido',
            'price.min' => "Valor mínimo: {$this->priceMinValue}",
            'price.max' => "Valor máximo: {$this->priceMaxValue}",

            'description.min' => "Tamanho máximo excedido: {$descMaxMsg}",

            'additionals.array' => 'Campo inválido',
        ];
    }
}
