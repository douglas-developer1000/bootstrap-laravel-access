@php
    /**
     * @see App\View\Components\Organisms\ChecksEnumField::class
     **/
    $errorMsgId = $hasSomeError($errors) ? uniqid('err_') : '';
@endphp
<label
    @class ([
        'fs-075',
        'text-danger' => !empty($errorMsgId),
    ])
    >{{ $label }}:</label
>
<div class="d-flex align-items-center gap-3 position-relative">
    @foreach ($enumCases as $enum)
        <div class="cursor-pointer">
            <x-molecules.input-check
                class-label="fs-075"
                name="{{ $key }}[{{ $enum->value }}]"
                error-name="{{ $key }}.{{ $enum->value }}"
                checked='{{ old("{$key}.{$enum->value}", $defaults->contains(fn($val) => $val === $enum)) }}'
                :errorMsgId="$errorMsgId"
            >
                {{ $enum::tryFrom($enum->value)->toString() }}
            </x-molecules.input-check>
        </div>
        @if (!empty($errorMsgId))
            <x-atoms.form-field-error
                id="{{ $errorMsgId }}"
                class="d-block"
            >
                {{ $errors->first() }}
            </x-atoms.form-field-error>
        @endif
    @endforeach
</div>
