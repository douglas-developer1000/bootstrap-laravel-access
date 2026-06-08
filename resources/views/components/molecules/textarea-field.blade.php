@php
    /**
     * @see view('pages.products.create')
     **/
@endphp
@props ([
    'id' => uniqid('el_'),
    'name',
    'errorName' => $name,
    'labelText' => NULL,
    'placeholder' => NULL,
    'value' => '',
    'required' => false,
    'position' => 'relative',

    'cols' => 30,
    'rows' => 10,
])
<div
    {{
        $attributes->class([
            'row',
            'm-0',
            'w-100',
            "position-{$position}"
        ])
    }}
>
    @if (!empty($labelText))
        <label
            for="{{ $id }}"
            class="form-label px-0 fs-075"
            >{{ $labelText }}:</label
        >
    @endif
    @php ($errorMsgId = $errors->has($errorName) ? uniqid('err_') : '')
    <textarea
        @class ([
            'form-control',
            'fs-085',
            'w-auto',
            'is-invalid' => $errors->has($errorName),
        ])
        name="{{ $name }}"
        id="{{ $id }}"
        @if ($required !== false)
            required
        @endif
        @if ($placeholder !== null)
            placeholder="{{ $placeholder }}"
        @endif
        cols="{{ $cols }}"
        rows="{{ $rows }}"
    >{{-- // prettier-ignore --}}{{ $value }}</textarea>
    @error ($errorName)
        <x-atoms.form-field-error id="{{ $errorMsgId }}">
            {{ $message }}
        </x-atoms.form-field-error>
    @enderror
</div>
