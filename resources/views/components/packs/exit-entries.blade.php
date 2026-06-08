@props ([
    'product',
    'entries' => [],
    'preContent' => NULL
])

<div {{ $attributes->class(['table-container']) }}>
    {{ $preContent }}
    <div class="product-name bg-white rounded-pill">
        <span class="fw-medium">Produto: </span
        ><span class="text-info-emphasis">{{ $product->name }}</span>
    </div>
    <div class="total">
        <div>Qtd. total:</div>
        <div class="value">000</div>
    </div>
    <x-organisms.extract-btn
        type="submit"
        form="extract-product-{{ $product->id }}"
    />
    <table class="table table-hover table-striped">
        <thead>
            <tr>
                <th
                    scope="col"
                    class="fw-medium"
                >
                    ID
                </th>
                <th
                    scope="col"
                    class="fw-medium"
                >
                    VALIDADE
                </th>
                <th
                    scope="col"
                    class="fw-medium"
                >
                    QUANT.
                </th>
                <th
                    scope="col"
                    @class ([
                            'fw-medium',
                            'text-danger' => $errors->has('entries')
                        ])
                >
                    USOS
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($entries as $entry)
                <tr>
                    <td><div class="row-data">{{ $entry->id }}</div></td>
                    <td>
                        <div class="row-data">{{ $entry->validity }}</div>
                    </td>
                    <td>
                        <div class="text-truncate row-data">
                            {{ $entry->sizeView }}
                        </div>
                    </td>
                    <td class="column-uses">
                        <div class="uses">
                            <input
                                class="use-input"
                                type="number"
                                min="0"
                                max="{{ $entry->qtyRemain }}"
                                value="{{ old("entries.{$product->id}.{$entry->id}", 0) }}"
                                name="entries[{{ $product->id }}][{{ $entry->id }}]"
                                readonly
                            />
                            <x-atoms.button class="btn btn-secondary add">
                                <i class="bi bi-plus-lg"></i>
                            </x-atoms.button>
                            <x-atoms.button class="btn btn-secondary sub">
                                <i class="bi bi-dash-lg"></i>
                            </x-atoms.button>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @error ('entries')
        <x-atoms.form-field-error
            class="d-block"
            style="--pos-top: auto; --pos-bottom: -1em"
        >
            {{ $errors->first('entries')  }}
        </x-atoms.form-field-error>
    @enderror
</div>
