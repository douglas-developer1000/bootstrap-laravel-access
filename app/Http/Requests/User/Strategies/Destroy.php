<?php

declare(strict_types=1);

namespace App\Http\Requests\User\Strategies;

use App\Http\Requests\Checker;
use App\Models\User;
use Illuminate\Validation\Rule;
use App\Rules\OnlySoftDelete;
use Illuminate\Foundation\Http\FormRequest;

final class Destroy implements Checker
{
    protected bool $forceDelete;

    public function __construct(FormRequest $request)
    {
        $this->forceDelete = collect($request->query() ?? [])->contains(
            fn($value, $key) => $key === 'trashed' && $value === '1'
        );
    }

    public function rules(): array
    {
        $rules = [
            'remotion' => [
                'required',
                'array',
                'min:1',
                Rule::doesntContain(User::role('super-admin')->get()->map(
                    fn($item) => \strval($item->id)
                )->all())
            ],
            'remotion.*' => [
                'integer',
                'exists:users,id'
            ],
        ];

        if ($this->forceDelete) {
            $rules['remotion.*'][] = new OnlySoftDelete();
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'remotion.required' => 'Remoção inválida',
            'remotion.array' => 'Remoção inválida',
            'remotion.min' => 'Remoção inválida',
            'remotion.doesnt_contain' => 'Remoção inválida',

            'remotion.*.integer' => 'Remoção inválida',
            'remotion.*.exists' => 'Remoção inválida',
        ];
    }
}
