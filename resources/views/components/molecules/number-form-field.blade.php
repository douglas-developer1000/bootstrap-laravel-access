@props ([
    'id' => uniqid('el_'),
    'name',
    'errorName' => $name,
    'type' => 'text',
    'labelText' => NULL,
    'placeholder' => NULL,
    'min' => 0,
    'value' => NULL,
    'required' => false,
    'position' => 'relative',
    'size' => 'stretch',
    'step' => '0.01',
    'lang' => 'en',
    'dtAttr' => []
])

<div
    {{ $attributes->class([
        'row',
        'm-0',
        'w-100',
        "position-{$position}",
    ]) }}
>
    @if (!empty($labelText))
        <label
            for="{{ $id }}"
            class="form-label px-0 fs-075"
            >{{ $labelText }}</label
        >
    @endif
    @php ($errorMsgId = $errors->has($errorName) ? uniqid('err_') : '')
    <input
        @class ([
            'form-control',
            'fs-085',
            'rounded-0',
            'text-start',
            'pe-0' => !$errors->has($errorName),
            'w-auto' => $size === 'auto',
            'is-invalid' => $errors->has($errorName),
        ])
        @if ($placeholder !== null)
            placeholder="{{ $placeholder }}"
        @endif
        name="{{ $name }}"
        id="{{ $id }}"
        type="number"
        value="{{ $value ?? $min }}"
        step="{{ $step }}"
        lang="{{ $lang }}"
        min="{{ $min }}"
        @if ($errors->has($errorName))
            aria-describedby="{{ $errorMsgId }}"
        @endif
        @if ($required !== false)
            required
        @endif
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
