@use ('App\Libraries\Enums\BillingPeriodEnum')
@push ('styling')
    @vite ([
        'resources/css/pages/generic/default.css',
        'resources/css/pages/generic/create.css'
    ])
@endpush
@push ('ecmascript-bottom')
    @vite ('resources/js/pages/generic/masks.ts')
@endpush

<x-layout title="Criar plano">
    <x-packs.header>
        <x-packs.page-heading-row heading="Criar plano" />
    </x-packs.header>
    <main class="bg-secondary-subtle create-main main-default">
        <section class="content bg-light">
            <x-molecules.block-error
                :keys="[
                    'additionals.array'
                ]"
            />
            <form
                method="post"
                class="create-form"
                action="{{ route('plans.store') }}"
            >
                @csrf
                <x-molecules.form-field
                    name="name"
                    type="text"
                    label-text="Nome:"
                    id="name-field"
                    placeholder="Insira o nome do plano"
                    required
                    value="{{ old('name', '') }}"
                />
                <x-molecules.select-field
                    label-text="Faturamento:"
                    name="billing_period"
                    placeholder="Selecione..."
                    aria-label="Selecione um tipo de faturamento"
                    required
                    size="auto"
                    :value="old('billing_period', '')"
                >
                    @foreach (BillingPeriodEnum::cases() as $type)
                        <option
                            @selected ($type->value == old('type', ''))
                            value="{{ $type->value }}"
                        >
                            {{ $type->toString() }}
                        </option>
                    @endforeach
                </x-molecules.select-field>
                <x-molecules.form-field
                    name="price"
                    label-text="Preço:"
                    placeholder="Insira o valor do plano"
                    required
                    value="{{ old('price', 0) }}"
                    lang="pt"
                    size="auto"
                    :dtAttr="['mask' => 'float-positive']"
                />
                <x-molecules.textarea-field
                    name="description"
                    labelText="Descrição (opcional)"
                    placeholder="Digite observações sobre o plano"
                    :value="old('description', '')"
                    rows="5"
                />
                <fieldset
                    class="border border-1 border-dark rounded-1 fieldset-tag"
                >
                    <legend class="field-legend bg-light">Papéis</legend>
                    <table class="table tabular-data">
                        <thead>
                            <tr>
                                <th
                                    scope="col"
                                    class="fw-medium"
                                >
                                    Nome
                                </th>
                                <th
                                    scope="col"
                                    class="fw-medium"
                                >
                                    Adicional
                                </th>
                                <th
                                    scope="col"
                                    class="fw-medium last-thdata"
                                    style="width: 4em"
                                >
                                    Ações
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($roles as $role)
                                <tr>
                                    <td>{{ $role->name }}</td>
                                    <td>
                                        <input
                                            type="checkbox"
                                            name="additionals[]"
                                            class="form-check-input"
                                            value="{{ $role->id }}"
                                        />
                                    </td>
                                    <td>
                                        <div
                                            class="w-100 d-flex justify-content-between gap-1 position-relative"
                                            style="--extract-btn-top: -0.5em"
                                        >
                                            <x-organisms.extract-btn
                                                type="submit"
                                                form="extract-role-{{ $role->id }}"
                                            />
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td
                                        colspan="3"
                                        class="no-values"
                                    >
                                        Sem papéis vinculados
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </fieldset>
                <x-atoms.submit-btn class="btn-primary create-btn">
                    Salvar
                </x-atoms.submit-btn>
            </form>
            @foreach ($roles as $role)
                <form
                    id="extract-role-{{ $role->id }}"
                    action="{{
                        route('roles.unmark', ['role' => $role->id])
                    }}"
                    method="post"
                    class="form-remotion"
                >
                    @csrf
                </form>
            @endforeach
        </section>
    </main>
</x-layout>
