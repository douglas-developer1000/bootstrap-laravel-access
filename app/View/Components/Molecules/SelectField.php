<?php

declare(strict_types=1);

namespace App\View\Components\Molecules;

use Illuminate\View\Component;
use ReflectionMethod;

final class SelectField extends Component
{
    public string $name;
    public string $errorName;
    public string $id;

    public string|null $value;

    public function __construct(
        string $name,
        string|null $errorName = NULL,
        string|null $id = NULL,

        public string|null $labelText = NULL,
        public string|null $placeholder = NULL,
        public string|null $ariaLabel = NULL,
        string|null $value = NULL,
        public bool $required = false,
        public bool $readonly = false,
        public bool $disabled = false,
        public bool $autofocus = false,
        /**
         * @var 'relative'|'absolute' $position
         */
        public string $position = 'relative',
        /**
         * @var 'stretch'|'auto' $size
         */
        public string $size = 'stretch',
    ) {
        $this->name = $name;
        $this->errorName = $errorName ?? $name;
        $this->id = $id ?? uniqid('el_');

        $this->value = $value ?? '';
    }

    public function render()
    {
        return view('components.molecules.select-field');
    }
}
