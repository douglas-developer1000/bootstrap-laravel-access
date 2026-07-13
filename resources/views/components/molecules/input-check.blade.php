@props ([
    'classCheck' => NULL,
    'classLabel' => NULL,
    'name' => NULL,
    'errorName' => $name ?? NULL,
    'id' => uniqid('el_'),
    'checked' => false,
    'errorMsgId' => NULL,
    'onchange' => NULL,
    'disabled' => false,
    'value' => NULL
])

<input
    id="{{ $id }}"
    @class ([
        'form-check-input',
        'cursor-pointer',
        $classCheck => !empty($classCheck),
        'is-invalid' => $errors->has($errorName) || !empty($errorMsgId),
    ])
    @checked ($checked ?? false)
    type="checkbox"
    value="{{ $value ?? '1' }}"
    @if (!empty($name))
        name="{{ $name }}"
    @endif
    @if (!empty($errorMsgId))
        aria-describedby="{{ $errorMsgId }}"
    @endif
    @if (!empty($onchange))
        onchange="{{ $onchange }}"
    @endif
    @if (!empty($disabled))
        @disabled($disabled)
    @endif
/>
<label
    for="{{ $id }}"
    @class ([
        'form-check-label',
        'cursor-pointer',
        'user-select-none',
        'text-truncate',
        $classLabel => !empty($classLabel),
    ])
    >{{ $slot }}</label
>
