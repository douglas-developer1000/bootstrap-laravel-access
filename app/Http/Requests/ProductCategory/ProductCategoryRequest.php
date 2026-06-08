<?php

declare(strict_types=1);

namespace App\Http\Requests\ProductCategory;

use App\Http\Requests\Checker;
use App\Http\Requests\CustomFormRequest;
use App\Http\Requests\ProductCategory\Strategies\Persistence;
use App\Http\Requests\ProductCategory\Strategies\Update;
use App\Http\Requests\ProductCategory\Strategies\DestroyGroup;
use App\Http\Requests\ProductCategory\Strategies\RestoreGroup;
use Exception;

final class ProductCategoryRequest extends CustomFormRequest
{

    protected function pickChecker(): Checker
    {
        $url = url()->current();

        switch ($url) {
            case route('product-categories.store'):
                return new Persistence();
            case route('product-categories.update', $this->route('category', 0)):
                return new Update($this->route('category'));
            case route('product-categories.group.destroy', [
                'key' => $this->route('key', 'key'),
                'productCategoryList' => 'list'
            ]):
                return new DestroyGroup();
            case route('product-categories.group.restore', [
                'key' => $this->route('key', 'key'),
                'productCategoryList' => 'trashed'
            ]):
                return new RestoreGroup();
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
