@use ('App\Libraries\Enums\BillingPeriodEnum')
@push ('styling')
    @vite ([
        'resources/css/pages/generic/default.css',
        'resources/css/pages/generic/create.css'
    ])
@endpush
@push ('ecmascript-bottom')
    @vite ([
        'resources/js/pages/generic/masks.ts',
        'resources/js/pages/plans/edit.ts',
    ])
@endpush

<x-layout title="Editar plano">
    <x-packs.header>
        <x-packs.page-heading-row>
            <x-slot:heading>
                Editar plano:
                <a
                    class="text-truncate text-decoration-none text-info border-0 ps-0 ms-2"
                    href="{{ route('plans.show', $plan->slug) }}"
                    >{{ $plan->name }}</a
                >
            </x-slot:heading>
        </x-packs.page-heading-row>
    </x-packs.header>
    <main class="bg-secondary-subtle create-main main-default">
        <section class="content bg-light">
            <x-molecules.block-error
                :keys="[
                    'roles',
                    'additionals.array',
                ]"
            />
            <form
                method="post"
                class="create-form"
                action="{{ route('plans.update', $plan->slug) }}"
            >
                @csrf
                @method ('PUT')
                <x-molecules.form-field
                    name="name"
                    type="text"
                    label-text="Nome:"
                    id="name-field"
                    placeholder="Insira o nome do plano"
                    required
                    value="{{ old('name', $plan->name) }}"
                />
                <x-molecules.select-field
                    label-text="Faturamento:"
                    name="billing_period"
                    placeholder="Selecione..."
                    aria-label="Selecione um tipo de faturamento"
                    required
                    size="auto"
                    :value="old('billing_period', $plan->billing_period->value)"
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
                    value="{{ old('price', $plan->price) }}"
                    lang="pt"
                    size="auto"
                    :dtAttr="['mask' => 'float-positive']"
                />
                <x-molecules.textarea-field
                    name="description"
                    labelText="Descrição (opcional)"
                    placeholder="Digite observações sobre o plano"
                    :value="old('description', $plan->description ?? '')"
                    rows="5"
                />
                <fieldset
                    @class ([
                        'border',
                        'border-1',
                        'rounded-1',
                        'fieldset-tag',

                        'border-dark' => !$errors->has('roles'),
                        'border-danger' => $errors->has('roles'),
                    ])
                >
                    <legend
                        @class ([
                            'field-legend',
                            'bg-light',
                            'text-danger' => $errors->has('roles')
                        ])
                    >
                        Papéis vinculados
                    </legend>
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
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($planRoles as $key => $role)
                                <tr>
                                    <td>
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            name="roles[]"
                                            id="role-{{ $key }}"
                                            value="{{ $role->name }}"
                                            data-pos="{{ $key }}"
                                            @checked (
                                                $boxChecked(
                                                    $errors,
                                                    ['initial' => true, 'old' => old('roles', [])],
                                                    fn(string $name) => $name === $role->name
                                                )
                                            )
                                        />
                                        <label
                                            for="role-{{ $key }}"
                                            class="form-check-label ms-1"
                                            >{{ $role->name }}</label
                                        >
                                    </td>
                                    <td>
                                        <input
                                            type="checkbox"
                                            name="additionals[]"
                                            class="form-check-input"
                                            value="{{ $role->id }}"
                                            @checked (
                                                $boxChecked(
                                                    $errors,
                                                    ['initial' => (bool) $role->pivot->additional, 'old' => old('additionals', [])],
                                                    fn(string|int $id) => $id === $role->id
                                                )
                                            )
                                        />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </fieldset>
                <fieldset
                    @class ([
                        'border',
                        'border-1',
                        'rounded-1',
                        'fieldset-tag',

                        'border-dark' => !$errors->has('roles'),
                        'border-danger' => $errors->has('roles'),
                    ])
                >
                    <legend
                        @class ([
                            'field-legend',
                            'bg-light',
                            'text-danger' => $errors->has('roles')
                        ])
                        >Novos papéis
                    </legend>
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
                                            @checked (
                                                $errors->isEmpty()
                                                    ? false
                                                    : $roles->contains(
                                                        fn($r) => $r->id === $role->id
                                                )
                                            )
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
                                        class="no-values text-center"
                                    >
                                        Sem novos papéis
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
                        route('roles.unmark', ['role' => $role->id, 'keep' => 1])
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
