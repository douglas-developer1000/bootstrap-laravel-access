<?php

declare(strict_types=1);

namespace App\Libraries\Helpers;

final class TimeProtection
{
    /**
     * Because the usleep native method only allows int values
     * and the microseconds' calculation results can be floats
     * then it's necessary convert them to int.
     */
    protected static function runSleep(float|int $microseconds): void
    {
        \usleep(
            \intval(
                \round($microseconds * 1000)
            )
        );
    }

    /**
     * Compare strings on secure way vs timing attacks
     */
    public static function secureCompare(string $known, string $input): bool
    {
        return \hash_equals($known, $input);
    }

    /**
     * Execute callback with timing protection
     */
    public static function execWithProtection(callable $callback, int $minExecutionTime = 500): mixed
    {
        $startTime = \microtime(true);
        $result = $callback();
        $elapsedTime = (\microtime(true) - $startTime) * 1000;

        if ($elapsedTime < $minExecutionTime) {
            $additionalDelay = ($minExecutionTime - $elapsedTime) + \random_int(0, 200);
            self::runSleep($additionalDelay);
        }

        return $result;
    }

    public static function gaussianDelay(float $mean = 300, float $stdDev = 100): void
    {
        $u1 = \random_int(1, 10000) / 10000;
        $u2 = \random_int(1, 10000) / 10000;

        // Box-Muller transform
        $randStdNormal = \sqrt(-2 * \log($u1)) * \sin(2 * M_PI * $u2);
        $delay = \max(0, $mean + $stdDev * $randStdNormal);

        self::runSleep($delay);
    }

    /**
     * Initially hash password to consume CPU as real checking.
     * After produce an additional randomic delay.
     */
    public function fakePasswordVerification(string $password): void
    {
        \bcrypt($password);

        self::gaussianDelay(100, 50);
    }
}
