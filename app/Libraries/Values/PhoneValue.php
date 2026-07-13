<?php

declare(strict_types=1);

namespace App\Libraries\Values;

use App\Exceptions\InvalidPhoneException;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;
use Closure;

final readonly class PhoneValue
{
    protected int|null $value;

    public function __construct(?string $value)
    {
        self::rule()->validate('phone', $value, function (string $message) {
            throw new InvalidPhoneException($message);
        });
        $this->value = $value !== NULL ? \intval(self::clear($value)) : $value;
    }

    public static function rule()
    {
        return (new class implements ValidationRule {

            protected int $phoneMinSize;

            protected int $phoneMaxSize;

            public function __construct()
            {
                $this->phoneMinSize = \intval(config('database.schema.sizes.generic.phone.min'));
                $this->phoneMaxSize = \intval(config('database.schema.sizes.generic.phone.max'));
            }

            public function validate(string $attribute, mixed $value, Closure $fail): void
            {
                if ($value === NULL) {
                    return;
                }
                $phone = Str::of(self::chopSeparators(\strval($value)));
                if (!$phone->isMatch('|^\d+$|')) {
                    $fail("Fomato inválido: XX XXXXX-XXXX");
                } else if (
                    $phone->length() < $this->phoneMinSize ||
                    $phone->length() > $this->phoneMaxSize
                ) {
                    $fail("Qtd de digitos inválidos: {$this->phoneMinSize} ou {$this->phoneMaxSize}");
                }
            }

            protected static function chopSeparators(?string $phone): ?string
            {
                if (!$phone) {
                    return $phone;
                }
                return Str::of($phone)->replaceMatches('|[\(\-\s\)]|', '')->toString();
            }
        });
    }

    protected static function clear(?string $phone): ?string
    {
        if (!$phone) {
            return $phone;
        }
        return Str::of($phone)->replaceMatches('|[^\d]|', '')->toString();
    }

    public function equals(PhoneValue $other): bool
    {
        return $this->value === $other->value;
    }

    public function getValue()
    {
        if ($this->value && \strlen(\strval($this->value)) === 9) {
            $withDDD = '51' . \strval($this->value);
            return \intval($withDDD);
        }
        return $this->value;
    }

    public function __toString()
    {
        if ($this->value === NULL) {
            return 'N/A';
        }
        $phone = Str::of(\strval($this->value));
        if ($phone->length() === 9) {
            return $phone->replaceMatches('|^(\d{5})(\d+)$|', '$1 $2')->toString();
        }
        return $phone->replaceMatches('|^(\d{2})(\d{5})(\d+)$|', '($1) $2-$3')->toString();
    }
}
