@props (['id', 'name', 'type' => 'text', 'label-text', 'placeholder' => NULL ])

<div class="row w-100">
    @if ($labelText !== null)
        <label
            for="{{ $id }}"
            class="form-label px-0 fs-075"
            >{{ $labelText }}</label
        >
    @endif
    <input
        class="form-control fs-085 rounded-0"
        @if ($placeholder !== null)
            placeholder="{{ $placeholder }}"
        @endif
        name="{{ $name }}"
        id="{{ $id }}"
        type="{{ $type }}"
    />
</div>
