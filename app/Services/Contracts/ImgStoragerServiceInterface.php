<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use Illuminate\Http\Request;

interface ImgStoragerServiceInterface
{
    public function persist(Request $request): ?string;

    public function remove(): bool;

    // public function getImgPath(): string;
}
