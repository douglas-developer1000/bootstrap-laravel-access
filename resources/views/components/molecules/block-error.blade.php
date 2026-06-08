@props ([
    'keys' => []
])

@php
    $keyList = collect($keys);
@endphp

@if (
    $keyList->isNotEmpty() && $keyList->contains(function (string $key) use ($errors) {
        return $errors->has($key);
    })
)
    <div
        {{
            $attributes->class([
                'p-3',
                'text-danger-emphasis',
                'bg-danger-subtle',
                'border',
                'border-danger-subtle',
                'rounded-3'
            ])
        }}
    >
        {{ $errors->first() }}
    </div>
@endif
