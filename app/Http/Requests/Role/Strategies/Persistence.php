<?php

declare(strict_types=1);

namespace App\Http\Requests\Role\Strategies;

use App\Http\Requests\Checker;
use Closure;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rules\Unique;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Collection;

final class Persistence implements Checker
{
    protected int $minSize;
    protected int $maxSize;
    public function __construct(protected ?Role $role = NULL)
    {
        $this->minSize = 3;
        $this->maxSize = Builder::$defaultStringLength;
    }
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                "min:{$this->minSize}",
                "max:{$this->maxSize}",
                Rule::unique('roles', 'name')->when(
                    $this->role,
                    fn(Unique $query) => $query->ignore($this->role->id, 'id')
                )
            ],
            'summary' => [
                'required',
                "min:{$this->minSize}",
                "max:{$this->maxSize}"
            ],
            'descriptions' => [
                'bail',
                'required',
                'array',
                function (string $attribute, mixed $value, Closure $fail) {
                    $list = collect($value);
                    if ($this->hasEmptyValues($list)) {
                        $fail('Valores inválidos');
                    } elseif ($list->duplicates()->isNotEmpty()) {
                        $fail('Remova as descrições repetidas');
                    }
                }
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Campo obrigatório',
            'name.min' => "Tamanho mínimo: {$this->minSize}",
            'name.max' => "Tamanho máximo: {$this->maxSize}",
            'name.unique' => 'Valor já utilizado',

            'summary.required' => 'Campo obrigatório',
            'summary.min' => "Tamanho mínimo: {$this->minSize}",
            'summary.max' => "Tamanho máximo: {$this->maxSize}",

            'descriptions.required' => 'Campo obrigatório',
            'descriptions.array' => 'Campo inválido',
        ];
    }

    protected function hasEmptyValues(array|Collection $values): bool
    {
        $list = \is_array($values) ? collect($values) : $values;
        return $list->contains(
            fn(string $value) => Str::of($value)->trim()->isEmpty()
        );
    }
}
