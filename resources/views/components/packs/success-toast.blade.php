@props (['delay' => 5000])

@push ('ecmascript-bottom')
    <script>
        window.toastShow = Boolean({{ session('toastShow', false) }});
    </script>
    @vite ('resources/js/components/packs/success-toast.ts')
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
        <div
            class="toast-header bg-success-subtle border-success-subtle text-success d-flex gap-2"
        >
            <i class="bi bi-check-circle"></i>
            <strong class="me-auto">Aviso</strong>
            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="toast"
                aria-label="Fechar"
            ></button>
        </div>
        <div class="toast-body">{{ session('toastMsg', '') }}</div>
    </div>
</div>
