@props (['id' => 'term-'.uniqid(), 'label-text', 'placeholder'])

<form
    method="get"
    class="d-flex align-items-end gap-3"
    action="{{ url()->current() }}"
>
    @foreach (collect(request()->query())->except('q') as $key => $value)
        <input
            type="hidden"
            name="{{ $key }}"
            value="{{ $value }}"
        />
    @endforeach

    <x-molecules.form-field
        :id="$id"
        name="q"
        :placeholder="$placeholder"
        :label-text="$labelText"
        autocomplete="no"
        value="{{ request()->query('q', '') }}"
    />
    <x-atoms.submit-btn
        class="btn-primary"
        py="py-1"
        px="px-3"
    >
        <i class="bi bi-search fs-6"></i>
    </x-atoms.submit-btn>
</form>
