@props (['details' => NULL])

@push ('styling')
    @vite ([
        'resources/css/components/packs/add-details-field.css',
        'resources/js/components/packs/add-details-field.ts'
    ])
@endpush

<fieldset class="details">
    <legend class="fs-075 m-0">Detalhes:</legend>
    <div class="input-section">
        <input
            type="text"
            class="key form-control"
        />
        <input
            type="text"
            class="value form-control"
        />
        <button
            class="btn btn-secondary btn-sm add-row cursor-pointer"
            type="button"
        >
            <i class="bi bi-plus-lg"></i>
        </button>
    </div>
    <table class="table table-hover table-striped">
        <thead>
            <tr>
                <th scope="col">Nome</th>
                <th scope="col">Valor</th>
                <th scope="col">-</th>
            </tr>
        </thead>
        <tbody>
            @forelse (json_decode($details ?? '[]') as $item)
                <tr>
                    <td><div class="text-truncate">{{ $item->key }}</div></td>
                    <td><div class="text-truncate">{{ $item->value }}</div></td>
                    <td>
                        <button class="btn btn-secondary btn-sm details-rm-btn">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </td>
                </tr>
            @empty
                <tr class="no-values">
                    <td colspan="3">Sem detalhes</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <input
        type="hidden"
        name="details"
        value="{{ $details ?? '[]' }}"
    />
</fieldset>
