<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Facades\TimingProtection;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class TimingProtectionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        return TimingProtection::execWithProtection(
            fn () => $next($request)
        );
    }
}
