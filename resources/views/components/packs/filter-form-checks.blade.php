<div {{ $attributes->class(['d-flex', 'flex-wrap']) }}>
    @foreach ($checkboxes as $key => $label)
        <x-organisms.filter-form-check
            :key="$key"
            :checked="$checkedStatus($key)"
        >
            {{ $label }}</x-organisms.filter-form-check
        >
    @endforeach
</div>
