<?php

namespace App\Services\Contracts;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

interface StockExitHandlerInterface
{
    /**
     * @param Collection<\App\Models\StockExit> $exits
     * @return void
     */
    public function handle(Request $request, Collection $exits): void;
}
