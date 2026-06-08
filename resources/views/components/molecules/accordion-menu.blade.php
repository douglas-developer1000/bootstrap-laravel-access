@props ([
    'id' => uniqid('id_'),
    'label' => '...',
    'items' => [],
])

@pushOnce ('styling')
    @vite ([
        'resources/css/components/molecules/accordion-menu.css'
    ])
@endPushOnce

<li
    id="{{ $id }}"
    data-reference="accordion-menu"
    class="nav-item text-center w-auto accordion accordion-flush"
>
    @php
        $controlledId = uniqid('control_');
    @endphp
    <div class="accordion-item">
        <h2 class="accordion-header">
            <button
                class="accordion-button collapsed"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#{{ $controlledId }}"
                aria-expanded="false"
                aria-controls="{{ $controlledId }}"
            >
                <div class="label position-relative w-100 h-100">
                    {{ $label }}
                </div>
            </button>
        </h2>
    </div>
    <ul
        id="{{ $controlledId }}"
        class="accordion-collapse collapse"
        data-bs-parent="#{{ $id }}"
    >
        {!! $items !!}
    </ul>
</li>
