<button
    {{ $attributes->class([
        'btn',
        'w-auto',
        'fs-075',
        'py-075',
        'px-4',
        'rounded-3'
    ])->merge(['type' => 'submit']) }}
>
    {{ $slot }}
</button>
