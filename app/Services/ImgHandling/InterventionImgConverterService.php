<?php

declare(strict_types=1);

namespace App\Services\ImgHandling;

use App\Services\Contracts\ImgConverterInterface;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Format;

class InterventionImgConverterService implements ImgConverterInterface
{
    public function __construct(protected string $key)
    {
        // ...
    }

    public function convertToWebp($resource)
    {
        try {
            return Image::decodeStream($resource)->encodeUsingFormat(
                Format::WEBP
            )->toStream();
        } catch (\Throwable $th) {
            Log::warning('Error (Webp convertion): ');
            Log::warning($th->getMessage());
        }
        return false;
    }
}
