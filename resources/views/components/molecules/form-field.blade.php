@props ([
    'id' => uniqid('el_'),
    'name',
    'errorName' => $name,
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
                'text-danger' => $errors->has($errorName)
            ])
            >{{ $labelText }}</label
        >
    @endif
    @php ($errorMsgId = $errors->has($errorName) ? uniqid('err_') : '')
    <input
        @class ([
            'form-control',
            'fs-085',
            'rounded-0',
            'pe-2',
            'text-start',
            'w-auto' => $size === 'auto',
            'is-invalid' => $errors->has($errorName),
        ])
        @if ($placeholder !== null)
            placeholder="{{ $placeholder }}"
        @endif
        name="{{ $name }}"
        id="{{ $id }}"
        type="{{ $type }}"
        value="{{ $value }}"
        lang="{{ $lang }}"
        @if ($errors->has($errorName))
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
    @error ($errorName)
        <x-atoms.form-field-error id="{{ $errorMsgId }}">
            {{ $message  }}
        </x-atoms.form-field-error>
    @enderror
</div>
