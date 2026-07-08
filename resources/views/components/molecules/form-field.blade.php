@props ([
    'id' => uniqid('el_'),
    'name',
    'errorName' => $name ?? NULL,
    'type' => 'text',
    'labelText' => NULL,
    'placeholder' => NULL,
    'value' => '',
    'required' => false,
    'autocomplete' => 'yes',
    'position' => 'relative',
    'size' => 'stretch',
    'lang' => 'pt',
    'dtAttr' => [],
])

<div
    {{ $attributes->class([
        'row',
        'm-0',
        'w-100',
        "position-{$position}",
    ]) }}
>
    @if (isset($labelText))
        <label
            for="{{ $id }}"
            @class ([
                'form-label',
                'px-0',
                'fs-075',
                'text-danger' => !empty($errorName) && $errors->has($errorName)
            ])
            >{{ $labelText }}</label
        >
    @endif
    @php ($errorMsgId = !empty($errorName) && $errors->has($errorName) ? uniqid('err_') : '')
    <input
        @class ([
            'form-control',
            'fs-085',
            'rounded-0',
            'pe-2',
            'text-start',
            'w-auto' => $size === 'auto',
            'is-invalid' => !empty($errorName) && $errors->has($errorName),
        ])
        @if ($placeholder !== null)
            placeholder="{{ $placeholder }}"
        @endif
        @if (!empty($name))
            name="{{ $name }}"
        @endif
        id="{{ $id }}"
        type="{{ $type }}"
        value="{{ $value }}"
        lang="{{ $lang }}"
        @if (!empty($errorName) && $errors->has($errorName))
            aria-describedby="{{ $errorMsgId }}"
        @endif
        @if ($required !== false)
            required
        @endif
        autocomplete="{{ $autocomplete }}"
        @foreach ($dtAttr as $attrKey => $attrVal)
            data-{{ $attrKey }}="{{ $attrVal }}"
        @endforeach
    />
    @if (!empty($errorName) && $errors->has($errorName))
        <x-atoms.form-field-error id="{{ $errorMsgId }}">
            {{ $message  }}
        </x-atoms.form-field-error>
    @endif
</div>
