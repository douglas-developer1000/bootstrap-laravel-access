<?php

declare(strict_types=1);

namespace App\Http\Requests\Supplier;

use App\Http\Requests\Checker;
use App\Http\Requests\CustomFormRequest;
use App\Http\Requests\Supplier\Strategies\Persistence;
use App\Http\Requests\Supplier\Strategies\Update;
use App\Http\Requests\Supplier\Strategies\DestroyGroup;
use App\Http\Requests\Supplier\Strategies\RestoreGroup;
use Exception;

final class SupplierRequest extends CustomFormRequest
{

    protected function pickChecker(): Checker
    {
        $url = url()->current();
        switch ($url) {
            case route('suppliers.store'):
                return new Persistence($this);
            case route('suppliers.update', $this->route('supplier', 0)):
                return new Update($this, $this->route('supplier'));
            case route('suppliers.group.destroy', [
                'key' => $this->route('key', 'key'),
                'supplierList' => 'list'
            ]):
                return new DestroyGroup();
            case route('suppliers.group.restore', [
                'key' => $this->route('key', 'key'),
                'supplierList' => 'trashed'
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
