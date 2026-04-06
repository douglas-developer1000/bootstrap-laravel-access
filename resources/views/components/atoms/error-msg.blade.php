<div
    {{ 
        $attributes->class([
            'alert',
            'alert-danger',
            'd-flex',
            'align-items-center',
            'd-flex',
            'gap-2',
            'py-1',
            'px-2',
            'fs-075'
        ])->merge(['role' => 'alert'])
    }}
>
    <i class="bi bi-exclamation-triangle"></i>
    <div>{{ $slot }}</div>
</div>
