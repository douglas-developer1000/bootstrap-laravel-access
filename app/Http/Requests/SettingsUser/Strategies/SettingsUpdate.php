<?php

declare(strict_types=1);

namespace App\Http\Requests\SettingsUser\Strategies;

use App\Http\Requests\Checker;
use App\Rules\PhoneValid;
use Illuminate\Support\Number;
use Illuminate\Support\Str;

final class SettingsUpdate implements Checker
{
    protected int $nameMinSize;
    protected int $nameMaxSize;
    protected int $phoneMinSize;
    protected int $phoneMaxSize;
    protected array $mimes;

    protected int $photoFileSize;

    public function __construct()
    {
        $this->nameMinSize = 2;
        $this->nameMaxSize = \intval(
            config('database.schema.sizes.user.name')
        );
        $this->phoneMinSize = 9;
        $this->phoneMaxSize = \intval(
            config('database.schema.sizes.register-order.phone')
        );
        $this->mimes = config('app.photo.mimes');
        $this->photoFileSize = config('app.photo.size');
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
                "min:{$this->phoneMinSize}",
                new PhoneValid($this->phoneMaxSize)
            ],
            'photo' => [
                'nullable',
                'mimes:jpeg,png',
                "max:{$this->photoFileSize}"
            ],
        ];
    }

    public function messages(): array
    {
        $mimes = implode(',', $this->mimes);
        $fileSize = Str::of(
            Number::fileSize(bytes: $this->photoFileSize * 1024)
        )->replaceFirst(' ', '')->wrap(' (', ')')->toString();

        return [
            'name.required' => 'Campo obrigatório',
            'name.min' => "Tamanho mínimo {$this->nameMinSize}",
            'name.max' => "Tamanho máximo excedido ($this->nameMaxSize)",

            'phone.min' => "Tamanho mínimo ({$this->phoneMinSize})",

            'photo.mimes' => "Extensões permitidas ($mimes)",
            'photo.max' => "Tamanho máximo de arquivo excedido {$fileSize}",
        ];
    }
}
