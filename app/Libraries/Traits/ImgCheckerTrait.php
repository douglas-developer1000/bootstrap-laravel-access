<?php

declare(strict_types=1);

namespace App\Libraries\Traits;

use Illuminate\Support\Number;
use Illuminate\Support\Str;

trait ImgCheckerTrait
{
    protected string $mimes;
    protected int $photoFileSize;

    protected function loadImgProps()
    {
        $this->mimes = implode(',', config('app.photo.mimes'));
        $this->photoFileSize = \intval(
            config('app.photo.size')
        );
    }

    protected function pickImgRules(string $key = 'img', bool $required = true): array
    {
        return [
            $key => [
                ...($required ? ['required'] : [
                    'bail',
                    'nullable',
                ]),

                "mimes:{$this->mimes}",
                "max:{$this->photoFileSize}"
            ],
        ];
    }

    protected function pickImgMessages(string $key = 'img', bool $required = true): array
    {
        $fileSize = Str::of(
            Number::fileSize(bytes: $this->photoFileSize * 1024)
        )->replaceFirst(' ', '')->wrap(' (', ')')->toString();

        return [
            ...($required ? ["{$key}.required" => 'Campo obrigatório'] : []),
            "{$key}.mimes" => "Extensões permitidas ($this->mimes)",
            "{$key}.max" => "Tamanho máximo de arquivo excedido {$fileSize}",
        ];
    }
}
