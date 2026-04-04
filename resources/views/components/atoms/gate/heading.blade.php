@push ('styling')
    @vite ('resources/css/components/atoms/gate/heading.css')
@endpush

<h1 class="gate-heading fs-4 text-nowrap fw-bold">{{ $slot }}</h1>
