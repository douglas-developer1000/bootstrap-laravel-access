<?php

declare(strict_types=1);

namespace App\Http\Requests\SettingsUser\Strategies;

use App\Http\Requests\Checker;
use App\Libraries\Traits\ImgCheckerTrait;
use App\Libraries\Values\PhoneValue;

final class SettingsUpdate implements Checker
{
    use ImgCheckerTrait;
    protected int $nameMinSize;
    protected int $nameMaxSize;
    public function __construct()
    {
        $this->nameMinSize = 2;
        $this->nameMaxSize = \intval(
            config('database.schema.sizes.user.name')
        );
        $this->loadImgProps();
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                "min:{$this->nameMinSize}",
                "max:{$this->nameMaxSize}"
            ],
            'phone' => [
                'nullable',
                'bail',
                PhoneValue::rule()
            ],
            ...$this->pickImgRules(key: 'photo', required: false),
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Campo obrigatório',
            'name.min' => "Tamanho mínimo {$this->nameMinSize}",
            'name.max' => "Tamanho máximo excedido ($this->nameMaxSize)",
            ...$this->pickImgMessages(key: 'photo', required: false),
        ];
    }
}
