@props (['delay' => 5000])

@push ('ecmascript-bottom')
    <script>
        window.toastShow = Boolean({{ session('toastShow', false) }});
    </script>
    @vite ('resources/js/components/packs/toast.ts')
@endpush

<div class="toast-container position-absolute top-0 start-0 p-3">
    <div
        id="liveToast"
        class="toast"
        role="alert"
        aria-live="assertive"
        aria-atomic="true"
        data-bs-delay="{{ $delay }}"
    >
        @if (session('toastType', 'success') === 'success')
            <x-organisms.success-toast-content />
        @else
            <x-organisms.error-toast-content />
        @endif
        <div class="toast-body">{{ session('toastMsg', '') }}</div>
    </div>
</div>
