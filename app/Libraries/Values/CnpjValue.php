<?php

declare(strict_types=1);

namespace App\Libraries\Values;

use App\Exceptions\InvalidCnpjException;
use Illuminate\Contracts\Validation\ValidationRule;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

final readonly class CnpjValue
{
    protected string|null $value;

    public function __construct(?string $value)
    {
        self::rule()->validate('cnpj', $value, function (string $message) {
            throw new InvalidCnpjException($message);
        });
        $this->value = self::clear($value);
    }

    public static function rule()
    {
        return (new class implements ValidationRule {
            protected function isValid(string|null $value): bool
            {
                if (!$value) {
                    return true;
                }

                $c = preg_replace('/((?![0-9A-Z]).)/', '', strtoupper($value));

                $b = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

                if (strlen($c) != 14) {
                    return false;
                }
                // Remove sequências repetidas como "111111111111"
                if (preg_match("/^{$c[0]}{14}$/", $c) > 0) {
                    return false;
                }

                for ($i = 0, $n = 0; $i < 12; $n += (ord($c[$i]) - 48) * $b[++$i]);

                if ($c[12] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
                    return false;
                }

                for ($i = 0, $n = 0; $i <= 12; $n += (ord($c[$i]) - 48) * $b[$i++]);

                if ($c[13] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
                    return false;
                }
                return true;
            }

            /**
             * Run the validation rule.
             *
             * @param  Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
             */
            public function validate(string $attribute, mixed $value, Closure $fail): void
            {
                if (!$this->isValid($value)) {
                    $fail('Campo inválido');
                }
            }
        });
    }

    public static function uniqueRule(string $table, int|string|null $except = NULL)
    {
        return (new class($table, $except) implements ValidationRule {
            public function __construct(
                protected string $table,
                protected int|string|null $except = NULL
            ) {
                // ...
            }

            /**
             * Run the validation rule.
             *
             * @param  Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
             */
            public function validate(string $attribute, mixed $value, Closure $fail): void
            {
                /**
                 * @var \App\Models\User $user
                 */
                $user = Auth::user();

                $cnpj = new CnpjValue($value);
                $exists = DB::table($this->table)->where([
                    'user_id' => $user->id,
                    'cnpj' => $cnpj->getValue(),
                ])->whereNotIn(
                    'id',
                    $this->except ? [$this->except] : []
                )->exists();

                if ($exists) {
                    $fail('Cnpj já utilizado');
                } else {
                    $exists = DB::table($this->table)->where([
                        'native' => 1,
                        'cnpj' => $cnpj->getValue(),
                    ])
                        ->whereNotIn(
                            'id',
                            $this->except ? [$this->except] : []
                        )
                        ->exists();

                    if ($exists) {
                        $fail('Cnpj já utilizado');
                    }
                }
            }
        });
    }

    protected static function clear(?string $cnpj): ?string
    {
        if (!$cnpj) {
            return $cnpj;
        }
        return preg_replace(
            '/[^A-Z0-9]/',
            '',
            $cnpj
        );
    }

    public function equals(CnpjValue $other): bool
    {
        return $this->value === $other->value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function __toString()
    {
        if (!$this->value) {
            return '';
        }
        return \sprintf(
            '%s.%s.%s/%s-%s',
            substr($this->value, 0, 2),
            substr($this->value, 2, 3),
            substr($this->value, 5, 3),
            substr($this->value, 8, 4),
            substr($this->value, 12, 2)
        );
    }
}
