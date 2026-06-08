<?php

declare(strict_types=1);

namespace App\Http\Requests\Product\Strategies;

use App\Http\Requests\Checker;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

final class Restore implements Checker
{
    protected int|string $id;

    public function __construct(FormRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $this->id = $user->id;

        $request->merge(['id' => $request->route('product', 0)]);
    }

    public function rules(): array
    {
        return [
            'id' => [
                'integer',
                Rule::exists('products', 'id')->where(function (Builder $query) {
                    $query->where('user_id', $this->id);
                })
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'id.integer' => 'Requisição inválida',
            'id.exists' => 'Requisição inválida',
        ];
    }
}
