@push ('styling')
    @vite ('resources/css/components/atoms/gate/card.css')
@endpush

<div
    {{
        $attributes->class([
            'gate-card',
            'd-flex',
            'flex-column',
            'justify-content-center',
            'align-items-center',
            'rounded-4',
            'p-4',
            'gap-4'
        ])
    }}
>
    {{ $slot }}
</div>
