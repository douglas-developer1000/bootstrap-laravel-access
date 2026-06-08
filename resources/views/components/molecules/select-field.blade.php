@php
    /**
     * @see App\View\Components\Molecules\SelectField::class
     * @see view('pages.products.create')
     * @see view('pages.products.categories.create')
     **/
@endphp
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
    @if ($labelText !== NULL)
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
    <select
        @class ([
            'form-select',
            'is-invalid' => $errors->has($errorName),
            'w-auto' => $size === 'auto'
        ])
        name="{{ $name }}"
        id="{{ $id }}"
        @if ($errors->has($errorName))
            aria-describedby="{{ $errorMsgId }}"
        @endif
        @if (!$errors->has($errorName) && $ariaLabel !== null)
            aria-label="{{ $ariaLabel }}"
        @endif
        @if ($required !== false)
            required
        @endif
        @if ($readonly)
            onfocus="this.initialValue = this.value"
            onchange="this.value = this.initialValue"
        @endif
        @if ($autofocus)
            autofocus
        @endif
    >
        @if ($placeholder !== null)
            <option
                value=""
                @disabled ($required !== false)
                @selected (empty($value))
            >
                {{ $placeholder }}
            </option>
        @endif
        {{ $slot }}
    </select>
    @error ($errorName)
        <x-atoms.form-field-error id="{{ $errorMsgId }}">
            {{ $message  }}
        </x-atoms.form-field-error>
    @enderror
    {{ $bottom ?? '' }}
</div>
