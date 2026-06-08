<?php

declare(strict_types=1);

namespace App\Http\Requests\Discount;

use App\Http\Requests\Checker;
use App\Http\Requests\CustomFormRequest;
use App\Http\Requests\Discount\Strategies\Persistence;
use App\Http\Requests\Discount\Strategies\Update;
use App\Http\Requests\Discount\Strategies\DestroyGroup;
use App\Http\Requests\Discount\Strategies\RestoreGroup;
use Closure;
use Exception;

final class DiscountRequest extends CustomFormRequest
{
    protected function pickChecker(): Checker
    {
        $url = url()->current();
        switch ($url) {
            case route('discounts.store'):
                return new Persistence($this);
            case route('discounts.update', $this->route('discount', 0)):
                return new Update($this, $this->route('discount'));
            case route('discounts.group.destroy', [
                'key' => $this->route('key', 'key'),
                'discountList' => 'list'
            ]):
                return new DestroyGroup();
            case route('discounts.group.restore', [
                'key' => $this->route('key', 'key'),
                'discountList' => 'trashed'
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
