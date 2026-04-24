@props ([
    'classCheck' => NULL,
    'classLabel' => NULL,
    'name' => '',
    'errorName' => $name,
    'id' => uniqid('el_'),
    'checked' => false,
    'ariaDescribedby' => NULL
])

<input
    id="{{ $id }}"
    class="{{
        implode(
            ' ',
            array_filter([
                'form-check-input',
                'cursor-pointer',
                $classCheck ?? NULL,
                $errors->has($errorName) ? 'is-invalid' : NULL
            ])
        )
    }}"
    type="checkbox"
    @if (trim($name))
        name="{{ $name }}"
    @endif
    @if ($ariaDescribedby !== NULL)
        aria-describedby="{{ $ariaDescribedby }}"
    @endif
    @checked ($checked ?? false)
    value="1"
/>
<label
    for="{{ $id }}"
    class="{{
        implode(
            ' ',
            array_filter([
                'form-check-label',
                'cursor-pointer',
                'user-select-none',
                $classLabel ?? NULL
            ])
        )
    }}"
    >{{ $slot }}</label
>
