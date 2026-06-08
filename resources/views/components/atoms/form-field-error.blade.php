@props (['id' => uniqid('err_')])

@push ('styling')
    @vite (['resources/css/components/atoms/form-field-error.css'])
@endpush

<div
    {{
        $attributes->class([
            'form-field-error-box',
            'invalid-feedback',
            'position-absolute',
            'end-0',
            'w-auto',
            'px-0',
            'fs-075'
        ])->merge(['id' => $id])
    }}
>
    {{ $slot  }}
</div>
