<?php

declare(strict_types=1);

namespace App\Services\Abstracts;

use App\Services\Contracts\ImgConverterInterface;
use App\Services\Contracts\ImgStoragerInterface;
use Illuminate\Http\Request;

abstract class AbstractImgConverter implements ImgStoragerInterface
{
    protected ImgConverterInterface $imgConverter;

    public function __construct(protected string $key)
    {
        $this->imgConverter = app(ImgConverterInterface::class, ['key' => $this->key]);
    }

    abstract protected function getOrCreateFolder(string $folderPath): ?string;

    protected function processImg(Request $request)
    {
        $file = $request->file($this->key);
        $origin = fopen($file->getRealPath(), 'r');
        if (\is_resource($origin)) {
            $resource = $this->imgConverter->convertToWebp($origin);
            fclose($origin);
            return $resource;
        }
        return $origin;
    }

    protected function generateRandomFilename($extension = 'png', $length = 16)
    {
        // Generate secure random bytes and convert to hex
        // Note: bin2hex doubles the length of bytes provided
        $randomString = bin2hex(random_bytes($length / 2));
        return implode('.', [$randomString, $extension]);
    }

    protected function buildPath(string ...$list): string
    {
        return implode(DIRECTORY_SEPARATOR, $list);
    }
}
