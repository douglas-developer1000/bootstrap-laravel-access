<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Contracts\ImgStoragerServiceInterface;
use Illuminate\Http\Request;

class ProfileService
{
    public function __construct(protected ImgStoragerServiceInterface $imgStorager)
    {
        // ...
    }

    public function storageProfileImg(Request $request): ?string
    {
        $path = $this->imgStorager->persist($request);
        if ($path) {
            $this->imgStorager->remove();
        }
        return $path;
    }
}
