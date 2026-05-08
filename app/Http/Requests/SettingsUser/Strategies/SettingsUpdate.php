<?php

declare(strict_types=1);

namespace App\Http\Requests\SettingsUser\Strategies;

use App\Http\Requests\Checker;
use App\Libraries\Values\PhoneValue;
use Illuminate\Support\Number;
use Illuminate\Support\Str;

final class SettingsUpdate implements Checker
{
    protected int $nameMinSize;
    protected int $nameMaxSize;
    protected string $mimes;

    protected int $photoFileSize;

    public function __construct()
    {
        $this->nameMinSize = 2;
        $this->nameMaxSize = \intval(
            config('database.schema.sizes.user.name')
        );
        $this->mimes = implode(',', config('app.photo.mimes'));
        $this->photoFileSize = \intval(
            config('app.photo.size')
        );
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
            'photo' => [
                'nullable',
                "mimes:{$this->mimes}",
                "max:{$this->photoFileSize}"
            ],
        ];
    }

    public function messages(): array
    {
        $fileSize = Str::of(
            Number::fileSize(bytes: $this->photoFileSize * 1024)
        )->replaceFirst(' ', '')->wrap(' (', ')')->toString();

        return [
            'name.required' => 'Campo obrigatório',
            'name.min' => "Tamanho mínimo {$this->nameMinSize}",
            'name.max' => "Tamanho máximo excedido ($this->nameMaxSize)",

            'photo.mimes' => "Extensões permitidas ($this->mimes)",
            'photo.max' => "Tamanho máximo de arquivo excedido {$fileSize}",
        ];
    }
}
