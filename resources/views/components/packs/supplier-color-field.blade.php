@use ('App\Libraries\Enums\SupplierColorEnum')
@props (['default' => NULL])

<div class="position-relative">
    <div
        @class ([
            'form-label',
            'px-0',
            'fs-075',
            'text-danger' => $errors->has('color'),
        ])
        >Cor:
    </div>
    <div class="row m-0 w-100 color-group">
        @php ($errorMsgId = $errors->has('color') ? uniqid('err_') : '')
        @foreach (array_column(SupplierColorEnum::cases(), 'value') as $index => $color)
            <div
                @class ([
                    'color-item',
                    'form-check',
                    'is-invalid' => $errors->has('color'),
                ])
            >
                <input
                    class="form-check-input"
                    type="radio"
                    name="color"
                    value="{{ $color }}"
                    @if ($errors->has('color'))
                        aria-describedby="{{ $errorMsgId }}"
                    @endif
                    id="{{ "color-{$index}" }}"
                    @checked (old('color', $default ?? '') === $color)
                />
                <label
                    class="form-check-label"
                    style="background-color: {{ $color }};"
                    for="{{ "color-{$index}" }}"
                ></label>
            </div>
        @endforeach
        @error ('color')
            <x-atoms.form-field-error id="{{ $errorMsgId }}">
                {{ $message  }}
            </x-atoms.form-field-error>
        @enderror
    </div>
</div>
