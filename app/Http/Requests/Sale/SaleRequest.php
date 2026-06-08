<?php

declare(strict_types=1);

namespace App\Http\Requests\Sale;

use App\Http\Requests\Checker;
use App\Http\Requests\CustomFormRequest;
use App\Http\Requests\Sale\Strategies\DestroyGroup;
use Exception;

final class SaleRequest extends CustomFormRequest
{
    protected function pickChecker(): Checker
    {
        $url = url()->current();

        switch ($url) {
            case route('sales.group.destroy', [
                'key' => $this->route('key', 'key'),
                'saleList' => 'list'
            ]):
                return new DestroyGroup();
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
