@props (['href', 'mobile-href', 'img-alt'])

@push ('styling')
    @vite ('resources/css/components/packs/gate-back-img.css')
@endpush

<div class="gate-back-img">
    <picture>
        <source
            srcset="{{ $href }}"
            media="(min-width: 768px)"
        />
        <img
            class="back-img"
            src="{{ $mobileHref  }}"
            alt="{{ $imgAlt }}"
        />
    </picture>
</div>
