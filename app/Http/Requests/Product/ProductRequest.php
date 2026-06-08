<?php

declare(strict_types=1);

namespace App\Http\Requests\Product;

use App\Http\Requests\Checker;
use App\Http\Requests\CustomFormRequest;
use App\Http\Requests\Product\Strategies\Persistence;
use App\Http\Requests\Product\Strategies\Update;
use App\Http\Requests\Product\Strategies\Destroy;
use App\Http\Requests\Product\Strategies\Restore;
use App\Http\Requests\Product\Strategies\RestoreGroup;
use Exception;

final class ProductRequest extends CustomFormRequest
{
    protected function pickChecker(): Checker
    {
        $url = url()->current();
        switch ($url) {
            case route('products.store'):
                return new Persistence();
            case route('products.update', $this->route('product', 0)):
                return new Update();
            case route('products.group.destroy'):
                return new Destroy($this);
            case route('products.restore', $this->route('product', 0)):
                return new Restore($this);
            case route('products.group.restore'):
                return new RestoreGroup($this);
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
