@props ([
    'id' => uniqid('el_'),
    'name',
    'errorName' => $name,
    'type' => 'text',
    'label-text' => NULL,
    'placeholder' => NULL,
    'value' => '',
    'required' => false,
    'autocomplete' => 'yes',
    'position' => 'relative'
])

<div class="row m-0 w-100 position-{{ $position }}">
    @if (isset($labelText))
        <label
            for="{{ $id }}"
            class="form-label px-0 fs-075"
            >{{ $labelText }}</label
        >
    @endif
    @php ($errorMsgId = $errors->has($errorName) ? uniqid('err_') : '')
    <input
        class="form-control fs-085 rounded-0 pe-0 @error($errorName) is-invalid @enderror"
        @if ($placeholder !== null)
            placeholder="{{ $placeholder }}"
        @endif
        name="{{ $name }}"
        id="{{ $id }}"
        type="{{ $type }}"
        value="{{ $value }}"
        @error ($errorName)
            aria-describedby="{{ $errorMsgId }}"
        @enderror
        @if ($required !== false)
            required
        @endif
        autocomplete="{{ $autocomplete }}"
    />
    @error ($errorName)
        <div
            id="{{ $errorMsgId }}"
            class="invalid-feedback position-absolute end-0 w-auto px-0 fs-075"
            style="top: -0.25rem; line-height: 1.0625rem"
        >
            {{ $message  }}
        </div>
    @enderror
</div>
