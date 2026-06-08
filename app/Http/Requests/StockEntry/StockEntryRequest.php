<?php

declare(strict_types=1);

namespace App\Http\Requests\StockEntry;

use App\Http\Requests\Checker;
use App\Http\Requests\CustomFormRequest;
use App\Http\Requests\StockEntry\Strategies\Persistence;
use Exception;

final class StockEntryRequest extends CustomFormRequest
{
    protected function pickChecker(): Checker
    {
        $url = url()->current();

        switch ($url) {
            case route('stocks.entries.store', ['product' => $this->route('product', 0)]):
                return new Persistence($this->route('product'));
            default:
                throw new Exception("Method Not Implemented", 1);
        }
    }
    public function rules(): array
    {
        return $this->pickChecker()->rules();
    }

    public function messages(): array
    {
        return $this->pickChecker()->messages();
    }
}
