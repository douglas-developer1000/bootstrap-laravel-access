@props (['src', 'alt', 'href' => NULL])

@push ('styling')
    @vite ('resources/css/components/atoms/gate/logo.css')
@endpush

@if ($href === NULL)
    <img
        {{ $attributes->class(['gate-logo']) }}
        src="{{ $src }}"
        alt="{{ $alt }}"
        loading="eager"
    />
@else
    <a href="{{ $href }}">
        <img
            {{ $attributes->class(['gate-logo']) }}
            src="{{ $src }}"
            alt="{{ $alt }}"
            loading="eager"
        />
    </a>
@endif
