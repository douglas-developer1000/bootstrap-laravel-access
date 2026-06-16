<?php

declare(strict_types=1);

namespace App\Http\Requests\Product\Strategies;

use App\Http\Requests\Checker;
use App\Http\Requests\LateValidationInterface;
use App\Models\Product;
use Illuminate\Database\Query\Builder;
use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

final class Destroy implements Checker
{
    public function __construct(LateValidationInterface $late)
    {
        $late->pushAfterValidation(
            function (Validator $validator) use (&$late) {
                $this->validateDeleteGroup(
                    $validator,
                    $late->getInput('remotion')
                );
            }
        );
    }

    public function rules(): array
    {
        return [
            'remotion' => [
                'required',
                'array',
                'min:1',
            ],
            'remotion.*' => [
                'integer',
                'exists:products,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'remotion.required' => 'Remoção inválida',
            'remotion.array' => 'Remoção inválida',
            'remotion.min' => 'Remoção inválida',

            'remotion.*.integer' => 'Remoção inválida',
            'remotion.*.exists' => 'Remoção inválida',
        ];
    }

    protected function validateDeleteGroup(Validator $validator, array $idList): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $count = Product::query()
            ->when(
                !$user->hasRole('super-admin'),
                fn(Builder $query) => $query->join(
                    'users',
                    'products.user_id',
                    '=',
                    'users.id'
                )->where([
                    'users.id' => $user->id
                ])
            )
            ->whereNull(
                'products.deleted_at'
            )
            ->whereIn('products.id', $idList)
            ->count('products.id');

        if ($count !== \count($idList)) {
            $validator->errors()->add(
                'remotion.*',
                'Remoção inválida'
            );
        }
    }
}
