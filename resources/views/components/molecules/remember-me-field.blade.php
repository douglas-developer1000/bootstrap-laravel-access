@props (['id' => uniqid('el_')])

<div {{ $attributes->class(['mb-0']) }}>
    <input
        class="form-check-input"
        style="cursor: pointer"
        type="checkbox"
        name="remember"
        id="{{ $id }}"
    />
    <label
        class="form-check-label user-select-none fs-075 ms-1"
        style="cursor: pointer; position: relative; top: -0.125rem"
        for="{{ $id }}"
        >Lembrar-me</label
    >
</div>
