<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use Illuminate\Http\Request;

interface ImgStoragerInterface
{
    public function persist(Request $request): ?string;

    public function remove(): bool;
}
