<?php

declare(strict_types=1);

namespace App\Rules;

use App\Services\ProductToExitHandlerService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

final class StockSalePricesToExitRule implements ValidationRule
{
    protected int $valueMinSize;
    protected int $valueMaxSize;
    public function __construct()
    {
        $this->valueMinSize = \intval(
            config('database.schema.sizes.generic.decimal.min')
        );
        $this->valueMaxSize = \intval(
            config('database.schema.sizes.generic.decimal.max')
        );
    }

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->isPriceListValid($value, $fail)) {
            return;
        }
        $rules = collect($value)->keys()->mapWithKeys(fn(int $id) => [
            "prices.{$id}" => [
                'bail',
                'decimal:0,2',
                "gt:{$this->valueMinSize}",
                "max:{$this->valueMaxSize}",
            ]
        ]);
        $messages = $rules->mapWithKeys(fn(array $rules, string $key) => [
            "$key.decimal" => 'Preço por item inválido',
            "$key.gt" => "Preço por item deve ser maior que {$this->valueMinSize}",
            "$key.max" => "Preço por item máximo: {$this->valueMaxSize}",
        ]);
        $validator = Validator::make([$attribute => $value], $rules->all(), $messages->all());

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    protected function isPriceListValid(mixed $value, Closure $fail): bool
    {
        $svc = app(ProductToExitHandlerService::class);
        $prices = collect($value);
        $idListStored = collect($svc->getProductsToExit());
        if (
            $prices->count() !== $idListStored->count() ||
            !$idListStored->every(fn(int $id) => $prices->has($id))
        ) {
            $fail('Requisição inválida');
            return false;
        }
        return true;
    }
}
