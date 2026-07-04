<?php

declare(strict_types=1);

namespace App\Facades;

use App\Libraries\Helpers\TimeProtection;
use Illuminate\Support\Facades\Facade;
use Override;

/**
 * @method static bool secureCompare(string $known, string $input)
 * @method static mixed execWithProtection(callable $callback, int $minExecutionTime = 500)
 * @method static void gaussianDelay(float $mean = 300, float $stdDev = 100)
 * 
 * @see TimeProtection::class
 */
final class TimingProtection extends Facade
{
    #[Override]
    protected static function getFacadeAccessor()
    {
        return 'TimingProtection';
    }
}
