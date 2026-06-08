<?php

declare(strict_types=1);

namespace App\View\Components\Organisms;

use Illuminate\View\Component;
use Illuminate\Support\Collection;
use Illuminate\Support\ViewErrorBag;
use InvalidArgumentException;
use ReflectionMethod;
use BackedEnum;

final class ChecksEnumField extends Component
{
    protected Collection $enumCases;
    public Collection|null $defaults;
    public function __construct(
        public string $key,
        public string $label,
        public string $enum,
        Collection|null $defaults = NULL,
    ) {
        if (!is_subclass_of($this->enum, BackedEnum::class)) {
            throw new InvalidArgumentException("Must be a BackedEnum");
        }
        $this->enumCases = collect($this->enum::cases());
        $this->defaults = $defaults ?? collect([]);
    }

    protected function errorKeys()
    {
        return collect([$this->key])->merge(
            collect(array_column($this->enum::cases(), 'value'))->map(
                fn($value) => "{$this->key}.{$value}"
            )
        );
    }

    public function hasSomeError(ViewErrorBag $errors): bool
    {
        $errorKeyList = $this->errorKeys();
        return collect($errors->keys())->contains(fn($key) => (
            preg_match("|^{$this->key}.|", $key) || $errorKeyList->contains(
                fn($errorKey) => $errorKey === $key
            )
        ));
    }

    public function render()
    {
        return view('components.organisms.checks-enum-field', [
            'enumCases' => $this->enumCases,
        ]);
    }
}
