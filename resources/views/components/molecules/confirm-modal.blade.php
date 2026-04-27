@props ([
    'id',
    'heading',
    'negativeText',
    'positiveText',
    'href' => '',
    'method' => ''
])

<div
    class="modal fade"
    id="confirmModal{{ $id }}"
    tabindex="-1"
    aria-labelledby="modalLabel{{ $id }}"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div
                    class="modal-title fs-5"
                    id='modalLabel{{ $id }}'
                >
                    {{ $heading }}
                </div>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Fechar"
                ></button>
            </div>
            <div class="modal-body">{{ $slot }}</div>
            <div class="modal-footer">
                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal"
                >
                    {{ $negativeText }}
                </button>
                <form
                    action="{{ $href }}"
                    method="post"
                >
                    {{ $method }}
                    @csrf
                    <button
                        type="submit"
                        class="btn btn-primary"
                        data-bs-dismiss="modal"
                    >
                        {{ $positiveText }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
