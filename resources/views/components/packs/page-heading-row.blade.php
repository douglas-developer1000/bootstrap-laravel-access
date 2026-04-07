@props (['heading'])

@push ('styling')
    @vite ('resources/css/components/packs/page-heading-row.css')
@endpush

<section {{ $attributes->class(['page-heading-row']) }}>
    <div class="heading">{{ $heading }}</div>
    {{ $slot }}
</section>
