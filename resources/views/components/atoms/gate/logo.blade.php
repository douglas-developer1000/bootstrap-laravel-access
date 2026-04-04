@props (['src', 'alt'])

@push ('styling')
    @vite ('resources/css/components/atoms/gate/logo.css')
@endpush

<img
    class="gate-logo"
    src="{{ $src }}"
    alt="{{ $alt }}"
    loading="eager"
/>
