@props ([
    'id' => 'term-'.uniqid(),
    'labelText',
    'placeholder',
    'keyTerm' => 'q'
])

<form
    method="get"
    class="d-flex align-items-end gap-3"
    action="{{ url()->current() }}"
>
    @foreach (collect(request()->query())->except($keyTerm) as $key => $value)
        <input
            type="hidden"
            name="{{ $key }}"
            value="{{ $value }}"
        />
    @endforeach

    <div class="d-flex flex-column">
        <x-molecules.form-field
            :id="$id"
            :name="$keyTerm"
            :placeholder="$placeholder"
            :label-text="$labelText"
            autocomplete="no"
            value="{{ request()->query($keyTerm, '') }}"
        />
        {{ $slot }}
    </div>
    <x-atoms.submit-btn
        class="btn-primary"
        py="py-1"
        px="px-3"
    >
        <i class="bi bi-search fs-6"></i>
    </x-atoms.submit-btn>
</form>
