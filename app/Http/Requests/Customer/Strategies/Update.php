<?php

declare(strict_types=1);

namespace App\Http\Requests\Customer\Strategies;

use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class Update extends Persistence
{
    protected $id;

    public function __construct($id = NULL)
    {
        parent::__construct();
        $this->id = $id;
    }

    public function rules(): array
    {
        $rules = parent::rules();
        return [
            ...$rules,
            'email' => [
                ...Str::of($rules['email'])->before('|unique')->explode('|'),
                Rule::unique('customers', 'email')->ignore($this->id ?? 0, 'id'),
            ]
        ];
    }
}
