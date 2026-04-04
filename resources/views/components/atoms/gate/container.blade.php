@push ('styling')
    @vite ('resources/css/components/atoms/gate/container.css')
@endpush

<div class="gate-container d-flex flex-column align-items-center">
    {{ $slot }}
</div>
