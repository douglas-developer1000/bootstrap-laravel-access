<?php

declare(strict_types=1);

namespace App\Http\Requests\User\Strategies;

use App\Http\Requests\Checker;
use App\Http\Requests\LateValidationInterface;
use App\Models\Plan;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Spatie\Permission\Models\Role;

final class FastPersistence implements Checker
{
    protected int $nameMinSize;

    protected int $nameMaxSize;

    public function __construct(LateValidationInterface $late)
    {
        $this->nameMinSize = 2;
        $this->nameMaxSize = \intval(
            config('database.schema.sizes.user.name')
        );

        $late->pushAfterValidation(
            /**
             * Verify if every role from list belongs to plan passed as input
             */
            function (Validator $validator) use (&$late) {
                $additionals = collect($late->getInput('additionals') ?? [])->unique();
                $plan = Plan::whereSlug($late->getInput('plan'))->first();

                if (
                    $additionals->isNotEmpty() && $plan->roles()->whereIn(
                        'roles.name',
                        $additionals->all()
                    )->get(['roles.id'])->count() !== $additionals->count()
                ) {
                    $validator->errors()->add('additionals', 'Recursos adicionais inválidos!');
                }
            }
        );
        $late->pushAfterValidation(
            /**
             * Verify if every role from list is aditional resource
             */
            function (Validator $validator) use (&$late) {
                $additionals = collect($late->getInput('additionals') ?? [])->unique();
                $plan = Plan::whereSlug($late->getInput('plan'))->first();
                if (
                    $validator->errors()->isEmpty() &&
                    $additionals->isNotEmpty() &&
                    $plan->roles()->whereIn('roles.name', $additionals->all())->get()->contains(
                        fn (Role $role) => $role->pivot->additional !== 1
                    )
                ) {
                    $validator->errors()->add('additionals', 'Recursos adicionais inválidos!');
                }
            }
        );
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                "min:{$this->nameMinSize}",
                "max:{$this->nameMaxSize}",
            ],
            'email' => [
                'email',
                Rule::unique('users', 'email'),
            ],
            'plan' => [
                'required',
                Rule::exists('plans', 'slug'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Campo obrigatório',
            'name.min' => "Tamanho mínimo {$this->nameMinSize}",
            'name.max' => "Tamanho máximo excedido ({$this->nameMaxSize})",

            'email.email' => 'Campo inválido',
            'email.unique' => 'Valor já utilizado',

            'plan.required' => 'Campo obrigatório',
            'plan.exists' => 'Campo inválido',
        ];
    }
}
