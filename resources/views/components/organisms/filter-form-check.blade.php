<form
    {{
        $attributes->class([
            'd-flex',
            'gap-2',
        ])
    }}
    method="get"
    action="{{ $action }}"
>
    @foreach ($qs as $qsKey => $qsValue)
        <input
            type="hidden"
            name="{!! $qsKey !!}"
            value="{!! $qsValue !!}"
        />
    @endforeach
    <input
        type="hidden"
        name="{!! $key !!}"
        value="{{ $nextValue }}"
    />
    <x-molecules.input-check
        :checked="$checked"
        onchange="this.parentElement.submit()"
    >
        {{ $slot }}
    </x-molecules.input-check>
</form>
