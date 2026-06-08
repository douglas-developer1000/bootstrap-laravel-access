@push ('styling')
    @vite ([
        'resources/css/components/organisms/extract-btn.css'
    ])
@endpush

<x-atoms.button {{ $attributes->class(['extract-btn']) }}>
    <i class="bi bi-x-circle-fill"></i>
</x-atoms.button>
