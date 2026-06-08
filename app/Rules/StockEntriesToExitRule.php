<?php

declare(strict_types=1);

namespace App\Rules;

use App\Models\Product;
use App\Models\StockEntry;
use App\Services\StockEntryService;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Translation\PotentiallyTranslatedString;

final class StockEntriesToExitRule implements ValidationRule
{
    protected StockEntryService $entrySvc;

    public function __construct()
    {
        $this->entrySvc = app(StockEntryService::class);
    }

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $list = collect($value);
        $products = $list->keys()->map(function (int $id) {
            /** @var Product $product */
            $product = Product::findOrFail($id);
            $this->validateProduct($product);
            return $product;
        });
        $this->validateEntries($value);
        $this->validateEntriesRelation($value, $products);

        $failMsg = 'Requisição inválida';
        foreach ($list->values() as $index => $entries) {
            if (
                !$this->areKeysAndValuesInteger($entries) ||
                !$this->areValuesNonNegative($entries) ||
                !$this->sumIsGreaterThanZero($entries, $failMsg)
            ) {
                $fail($failMsg);
                break;
            }
            if (!$this->isExitQuantitiesValid($products[$index], $entries)) {
                $fail($failMsg);
                break;
            }
        }
    }

    /**
     * Check if each product and each entry shares the same database relationship
     */
    protected function validateEntriesRelation(mixed $value, Collection $products)
    {
        collect($value)->values()->each(function (array $entries, $index) use (&$products) {
            $product = $products->get($index);
            collect($entries)->each(function (mixed $qty, int $key) use (&$product) {
                $relation = StockEntry::where([
                    'product_id' => $product->id,
                    'id' => $key
                ])->exists();

                if (!$relation) {
                    throw new AuthorizationException();
                }
            });
        });
    }

    /**
     * Check if all entries belong to authenticated user
     */
    protected function validateEntries(mixed $value): void
    {
        Gate::authorize(
            'spend',
            [
                StockEntry::class,
                collect($value)->flatMap(
                    fn(array $innerList) => collect($innerList)->keys()
                )->unique()->all()
            ]
        );
    }

    /**
     * Check if the product belongs to authenticated user
     */
    protected function validateProduct(Product $product): void
    {
        Gate::authorize(
            'useOnExit',
            [
                Product::class,
                $product
            ]
        );
    }

    /**
     * Ensure all keys and values are integers
     */
    protected function areKeysAndValuesInteger(array $inputList): bool
    {
        $isInteger = fn(string|int $input) => ctype_digit(\strval($input));

        return collect($inputList)->every(fn(string|int $value, string|int $key) => (
            $isInteger($value) && $isInteger($key)
        ));
    }

    /**
     * Ensure each quantity on the list are greater or equal to zero (non-negative)
     */
    protected function areValuesNonNegative(array $inputList): bool
    {
        return collect($inputList)->every(
            fn(string|int $value) => \intval($value) >= 0
        );
    }

    /**
     * Ensure sum of all quantities is greater or equal to one (non-zero)
     */
    protected function sumIsGreaterThanZero(array $inputList, string &$failMsg): bool
    {
        $condition = collect($inputList)->sum(
            fn(string|int $value) => \intval($value)
        ) > 0;
        if (!$condition) {
            $failMsg = 'Defina uma quantidade a ser utilizada';
        }
        return $condition;
    }

    /**
     * Ensure all used exit quantities are lower or equal to its entries' quantities
     */
    protected function isExitQuantitiesValid(Product $product, array $inputList): bool
    {
        $resultRemain = $this->entrySvc->getRemainStockEntries($product);
        return collect($inputList)->every(function ($qty, $key) use (&$resultRemain) {
            $entry = $resultRemain->first(fn($row) => $row->id === $key);
            return \intval($qty) <= \intval($entry->qtyRemain);
        });
    }
}
