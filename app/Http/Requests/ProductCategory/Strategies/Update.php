<?php

declare(strict_types=1);

namespace App\Http\Requests\ProductCategory\Strategies;

use App\Http\Requests\ProductCategory\Strategies\Persistence;
use App\Models\ProductCategory;
use Closure;
use Override;

final class Update extends Persistence
{
    protected ProductCategory $category;

    public function __construct(ProductCategory $category)
    {
        parent::__construct();

        $this->category = $category;
    }

    protected function prepareRules(array $rules): array
    {
        $nameRules = collect($rules['name']);

        /** @var \Illuminate\Validation\Rules\Unique $uniqueRule */
        $uniqueRule = $nameRules->offsetGet($nameRules->count() - 1);

        $uniqueRule->ignore(
            $this->category->id,
            'id'
        );
        return $rules;
    }

    #[Override]
    public function rules(): array
    {
        $parentRules = parent::rules();
        return [
            ...$this->prepareRules($parentRules),
            'inheritance' => [
                ...$parentRules['inheritance'],
                "different:id",
                function (string $attribute, mixed $value, Closure $fail) {
                    $inheritance = ProductCategory::withTrashed()->findOrFail($value);
                    if ($inheritance->isDescendant($this->category)) {
                        $fail('Herança inválida');
                    }
                }
            ],
        ];
    }

    #[Override]
    public function messages(): array
    {
        $parentRules = parent::messages();
        return [
            ...$parentRules,

            'inheritance.different' => 'Herança inválida'
        ];
    }
}
