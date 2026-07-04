@push ('styling')
    @vite ([
        'resources/css/pages/generic/default.css',
        'resources/css/pages/generic/create.css',
    ])
@endpush

<x-layout title="Criar Usuário">
    <x-packs.header>
        <x-packs.page-heading-row heading="Criar Usuário" />
    </x-packs.header>
    <main class="bg-secondary-subtle create-main main-default">
        <section class="content bg-light">
            <x-molecules.block-error
                :keys="[
                    'additionals',
                ]"
            />
            <form
                method="post"
                class="create-form"
                action="{{ route('users.store') }}"
            >
                @csrf
                <x-molecules.form-field
                    name="name"
                    type="text"
                    label-text="Nome:"
                    placeholder="Insira o nome do usuário"
                    required
                    value="{{ old('name', '') }}"
                />
                <x-molecules.form-field
                    name="email"
                    type="email"
                    label-text="E-mail:"
                    placeholder="Insira o e-mail do usuário"
                    required
                    value="{{ old('email', '') }}"
                    autocomplete="no"
                />
                <x-molecules.form-field
                    name="password"
                    type="password"
                    label-text="Senha:"
                    placeholder="Insira a senha do usuário"
                    required
                    value="{{ old('password', '') }}"
                    autocomplete="no"
                />
                <x-packs.plan-license-fields
                    :plans="$plans"
                    :additionalRoles="$additionalRoles"
                    :required="true"
                />

                {{-- <div
                    class="d-flex align-items-top column-gap-3 justify-content-start"
                >
                    <x-molecules.select-field
                        class="w-auto"
                        label-text="Planos"
                        placeholder="Selecione..."
                        aria-label="Selecione um plano"
                        name="plan"
                        size="auto"
                        required
                        :value="old('plan', '')"
                    >
                        @foreach ($plans as $plan)
                            <option
                                @selected (old('plan') === $plan->slug)
                                value="{{ $plan->slug }}"
                            >
                                {{ $plan->name }}
                            </option>
                        @endforeach
                    </x-molecules.select-field>
                    <div class="d-flex gap-2 mt-4">
                        <x-molecules.input-check
                            class-label="fs-075"
                            name="recurring"
                            checked="{{ old('recurring', false) }}"
                        >
                            Plano recorrente
                        </x-molecules.input-check>
                    </div>
                </div>
                <div class="form-label px-0 fs-075">Recursos adicionais:</div>
                <div
                    class="additional-placeholder border border-secondary rounded p-1"
                >
                    Selecione um plano...
                </div>
                @foreach ($additionalRoles as $slug => $roles)
                    <ul
                        class="list-group"
                        data-slug="{{ $slug }}"
                    >
                        @forelse ($roles as $role)
                            <li class="list-group-item">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    name="additionals[]"
                                    id="{{ $role->name }}"
                                    value="{{ $role->name }}"
                                    @checked (
                                        $boxChecked(
                                            $errors,
                                            ['initial' => false, 'old' => old('additionals', [])],
                                            fn(string $name) => $name === $role->name
                                        )
                                    )
                                />
                                <label
                                    for="{{ $role->name }}"
                                    >{{ $role->name }}</label
                                >
                            </li>
                        @empty
                            <li class="list-group-item text-danger">
                                Sem recursos adicionais
                            </li>
                        @endforelse
                    </ul>
                @endforeach --}}
                <x-atoms.submit-btn class="btn-primary create-btn">
                    Criar
                </x-atoms.submit-btn>
            </form>
        </section>
        <x-packs.toast />
    </main>
</x-layout>
