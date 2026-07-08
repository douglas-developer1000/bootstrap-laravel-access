@push ('styling')
    @vite ([
        'resources/css/pages/generic/default.css',
        'resources/css/pages/generic/create.css'
    ])
@endpush
@push ('ecmascript-bottom')
    @vite ('resources/js/pages/roles/role-descriptions.ts')
@endpush

<x-layout title="{{ $title ?? 'Criar papel' }}">
    <x-packs.header>
        <x-packs.page-heading-row heading="{{ $title ?? 'Criar papel' }}" />
    </x-packs.header>
    <main class="bg-secondary-subtle create-main main-default">
        <section class="content bg-light">
            <form
                method="post"
                class="create-form"
                action="{{ $action ?? route('roles.store') }}"
            >
                @csrf
                @method ($method ?? 'POST')
                <x-molecules.form-field
                    name="name"
                    type="text"
                    label-text="Nome:"
                    id="name-field"
                    placeholder="Insira o nome do papel"
                    required
                    value="{{ old('name', $old['name'] ?? '') }}"
                />
                <x-molecules.form-field
                    name="summary"
                    type="text"
                    label-text="Resumo:"
                    id="summary-field"
                    placeholder="Insira o resumo do papel"
                    required
                    value="{{ old('summary', $old['summary'] ?? '') }}"
                />
                <fieldset
                    @class([
                        'border',
                        'border-1',
                        'border-dark',
                        'rounded-1',
                        'fieldset-tag',
                        'position-relative',
                        'border-danger' => $errors->has('descriptions'),
                        'text-danger' => $errors->has('descriptions'),
                    ])
                >
                    <legend class="field-legend bg-light">Descrições:</legend>
                    @error('descriptions')
                        <div
                            class="position-absolute text-danger fs-075 translate-three-quarter-y top-0 end-0 px-2"
                        >
                            {{ $errors->first('descriptions') }}
                        </div>
                    @enderror
                    <div class="d-flex gap-2">
                        <x-molecules.form-field
                            id="description-input"
                            type="text"
                            placeholder="Insira uma nova descrição"
                            class="mb-3"
                        />
                        <x-atoms.button
                            format="btn"
                            type="button"
                            class="btn-secondary align-self-start"
                            id="btn-in"
                        >
                            <i class="bi bi-plus-lg"></i>
                        </x-atoms.button>
                    </div>
                    @php
                        $oldDescriptions = old('descriptions', $descriptions);
                    @endphp
                    <ul
                        id="description-list"
                        @class([
                            'list-group',
                            'empty' => empty($oldDescriptions)
                        ])
                    >
                        @forelse ($oldDescriptions as $description)
                            <li class="list-group-item position-relative" data-description="{{ $description }}">
                                <span>
                                    {{ $description }}
                                </span>
                                <input type="hidden" name="descriptions[]" value="{{ $description }}">
                            </li>
                        @empty
                            <li class="list-group-item text-danger bg-secondary-subtle">Sem Descrições</li>
                        @endforelse
                    </ul>
                </fieldset>
                <x-atoms.submit-btn class="btn-primary create-btn">
                    Salvar
                </x-atoms.submit-btn>
            </form>
        </section>
    </main>
</x-layout>
