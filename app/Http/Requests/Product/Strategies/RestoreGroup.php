<?php

declare(strict_types=1);

namespace App\Http\Requests\Product\Strategies;

use App\Http\Requests\Checker;
use App\Http\Requests\LateValidationInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Validator;

final class RestoreGroup implements Checker
{
    public function __construct(LateValidationInterface $late)
    {
        $late->pushAfterValidation(
            function (Validator $validator) use (&$late) {
                $this->validateRestoreGroup(
                    $validator,
                    $late->getInput('restoration')
                );
            }
        );
    }

    public function rules(): array
    {
        return [
            'restoration' => [
                'required',
                'array',
                'min:1',
            ],
            'restoration.*' => [
                'integer',
                'exists:products,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'restoration.required' => 'Restauração inválida',
            'restoration.array' => 'Restauração inválida',
            'restoration.min' => 'Restauração inválida',

            'restoration.*.integer' => 'Restauração inválida',
            'restoration.*.exists' => 'Restauração inválida',
        ];
    }

    protected function validateRestoreGroup(Validator $validator, array $idList): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $data = DB::table('products', 'prod')
            ->join(
                'users',
                'prod.user_id',
                '=',
                'users.id'
            )
            ->where('prod.user_id', '=', $user->id)
            ->whereNotNull('prod.deleted_at')
            ->whereIn('prod.id', $idList)
            ->get('prod.id');

        $ok = $data->count() === \count($idList);
        if (!$ok) {
            $validator->errors()->add(
                'restoration.*',
                'Restauração inválida'
            );
        }
    }
}
