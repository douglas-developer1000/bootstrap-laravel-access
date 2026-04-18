<?php

declare(strict_types=1);

namespace App\Services\Contracts;

interface ImgConverterInterface
{
    /**
     * Convert an image buffer to a image buffer with webp extension
     * 
     * @param resource $resource
     * @return bool|resource The resource pointing to image converted
     */
    public function convertToWebp($resource);
}
