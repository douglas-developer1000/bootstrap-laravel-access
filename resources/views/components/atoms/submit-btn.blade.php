@props (['px' => 'px-4', 'py' => 'py-075'])

<button
    {{ $attributes->class([
        'btn',
        'w-auto',
        'fs-075',
        $py,
        $px,
        'rounded-3'
    ])->merge(['type' => 'submit']) }}
>
    {{ $slot }}
</button>
