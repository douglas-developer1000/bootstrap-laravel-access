@use ('App\Models\Supplier')
@use ('App\Models\User')
@push ('styling')
    @vite ([
        'resources/css/pages/generic/default.css',
        'resources/css/pages/generic/create.css',
        'resources/css/pages/suppliers/create.css',
    ])
@endpush
@push ('ecmascript-bottom')
    @vite ('resources/js/pages/generic/masks.ts')
@endpush

<x-layout title="{{ $title ?? 'Cadastrar Fornecedor' }}">
    <x-packs.header>
        <x-packs.page-heading-row heading="{{ $title ?? 'Cadastrar Fornecedor' }}">
            <div class="dropdown top-right-item">
                <x-atoms.button
                    class="btn-secondary dropdown-toggle"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                >
                    <i class="bi bi-menu-button-wide"></i>
                </x-atoms.button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <x-atoms.button
                            class="dropdown-item d-flex gap-2"
                            format="anchor"
                            href="{{ route('suppliers.index') }}"
                            :disabled="!$hasAccess('viewAny', Supplier::class)"
                        >
                            <i class="bi bi-buildings"></i>
                            <span>Fornecedores</span>
                        </x-atoms.button>
                    </li>
                </ul>
            </div>
        </x-packs.page-heading-row>
    </x-packs.header>
    <main class="bg-secondary-subtle create-main main-default">
        <section class="content bg-light">
            <form
                class="create-form"
                action="{{ $action ?? route('suppliers.store') }}"
                method="post"
                @can('beSuperAdmin', User::class)
                    enctype="multipart/form-data"
                @endcan
            >
                @csrf
                @method($method ?? 'POST')
                <x-molecules.form-field
                    name="name"
                    type="text"
                    label-text="Nome:"
                    id="name-field"
                    placeholder="Insira o nome"
                    required
                    value="{{ old('name', $supplier?->name ?? '') }}"
                />
                <x-molecules.form-field
                    name="cnpj"
                    type="text"
                    label-text="CNPJ (opcional):"
                    id="cnpj-field"
                    placeholder="Insira o cnpj"
                    value="{{ old('cnpj', $supplier?->cnpj ?? '') }}"
                    :dtAttr="['mask' => 'cnpj']"
                />
                @can('beSuperAdmin', User::class)
                    <x-molecules.form-field
                        name="img"
                        type="file"
                        label-text="Foto:"
                        placeholder="Insira a foto do fornecedor"
                    />
                @endcan
                @cannot('beSuperAdmin', User::class)
                    <x-packs.supplier-color-field :default="$supplier?->color ?? null" />
                @endcannot

                <x-molecules.textarea-field
                    name="obs"
                    labelText="Observação (opcional)"
                    placeholder="Digite observações sobre o fornecedor"
                    :value="old('obs', $supplier?->obs ?? '')"
                    rows="5"
                />
                <input
                    type="hidden"
                    name="native"
                    @can('beSuperAdmin', User::class)
                        value="{{ \intval($supplier?->native ?? true)  }}"
                    @endcan
                    @cannot('beSuperAdmin', User::class)
                        value="{{ \intval($supplier?->native ?? false)  }}"
                    @endcannot
                />
                <x-atoms.submit-btn class="btn-primary create-btn">
                    Salvar
                </x-atoms.submit-btn>
            </form>
        </section>
    </main>
</x-layout>
