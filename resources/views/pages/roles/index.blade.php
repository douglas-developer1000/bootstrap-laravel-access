@use ('App\Facades\DateFormatter')
@push ('styling')
    @vite ([
        'resources/css/pages/generic/index.css',
        'resources/css/pages/generic/table.css'
    ])
@endpush
@push ('ecmascript-bottom')
    @vite ([
        'resources/js/pages/generic/realocate-markups.ts',
        'resources/js/pages/generic/multiselection.ts',
    ])
@endpush

<x-layout title="Lista de Papeis">
    <x-packs.header>
        <x-packs.page-heading-row
            heading="Lista de Papeis"
            class="page-heading-row-custom"
        >
            <x-atoms.button
                class="btn-secondary"
                format="anchor"
                href="{{ route('roles.create') }}"
            >
                <i class="bi bi-plus h-1"></i>
            </x-atoms.button>
        </x-packs.page-heading-row>
    </x-packs.header>
    <main class="bg-secondary-subtle list-main">
        <section class="content bg-light">
            <x-molecules.block-error :keys="['remotion', 'remotion.*']" />
            <div class="d-flex flex-wrap justify-content-between row-gap-2">
                <x-packs.term-search
                    label-text="Nome:"
                    placeholder="Insira o nome do papel"
                />
                <div
                    class="d-flex justify-content-end flex-grow-1 column-gap-2"
                >
                    <x-organisms.confirm-rm-group-btn
                        route="roles.group.destroy"
                        heading="Remover estes papéis?"
                        positive-text="Remover papéis"
                        title="Remover vários papéis"
                    >
                        Isso removerá os papéis selecionados permanentemente.
                    </x-organisms.confirm-rm-group-btn>
                </div>
                <div class="d-flex gap-2 w-100 flex-wrap">
                    @if ($filterVisibility['for-plan'])
                        <x-organisms.filter-form-check
                            key="for-plan"
                            :checked="request()->boolean('for-plan')"
                            class="py-2"
                        >
                            Reservados para plano</x-organisms.filter-form-check
                        >
                    @endif
                    @if ($filterVisibility['no-user'])
                        <x-organisms.filter-form-check
                            key="no-user"
                            :checked="request()->boolean('no-user')"
                            class="py-2"
                        >
                            Sem usuário</x-organisms.filter-form-check
                        >
                    @endif
                    @if ($filterVisibility['no-plan'])
                        <x-organisms.filter-form-check
                            key="no-plan"
                            :checked="request()->boolean('no-plan')"
                            class="py-2"
                        >
                            Sem plano</x-organisms.filter-form-check
                        >
                    @endif
                </div>
            </div>
            <x-molecules.table-index qtyBtns="1">
                <x-slot:cols>
                    <col class="col-remain-created_at" />
                </x-slot:cols>
                <thead>
                    <tr>
                        <th scope="col">
                            <input
                                type="checkbox"
                                class="form-check-input cursor-pointer multiselection-all"
                            />
                        </th>
                        <x-atoms.table-head sort="name">
                            Nome</x-atoms.table-head
                        >
                        <x-atoms.table-head
                            default
                            colRemain
                            sort="created_at"
                        >
                            Criação</x-atoms.table-head
                        >
                        <th
                            scope="col"
                            class="last-thdata"
                        >
                            Ações
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($models($list) as $role)
                        <tr>
                            <td>
                                <input
                                    type="checkbox"
                                    value="{{ $role->id }}"
                                    class="form-check-input cursor-pointer multiselection-item"
                                    @if ($role->name === 'super-admin' || $role->name === 'user')
                                        disabled
                                    @endif
                                />
                            </td>
                            <td>
                                <a
                                    class="ellipsis text-decoration-none text-info"
                                    href="{{ route('roles.show', ['role' => $role->id]) }}"
                                    title="Visualizar papel"
                                    >{{ $role->name }}</a
                                >
                            </td>
                            <td>
                                {{ DateFormatter::formatToDate($role->created_at) }}
                            </td>
                            <td class="dropdown dropstart">
                                <x-atoms.button
                                    class="btn-secondary dropdown-toggle"
                                    data-bs-toggle="dropdown"
                                    aria-expanded="false"
                                >
                                    <i class="bi bi-menu-button-wide"></i>
                                </x-atoms.button>
                                <ul
                                    class="dropdown-menu dropdown-menu-start action-btns"
                                >
                                    <li>
                                        <div class="position-relative">
                                            @if ($role->inRolesCart)
                                                <span
                                                    class="position-absolute end-0 top-0 badge rounded-pill bg-danger p-0 z-1"
                                                    ><i class="bi bi-plus"></i
                                                ></span>
                                            @endif
                                            <form
                                                action="{{
                                                    route(
                                                        $role->inRolesCart ? 'roles.unmark' : 'roles.mark',
                                                        ['role' => $role->id]
                                                    )
                                                }}"
                                                method="post"
                                            >
                                                @csrf
                                                <x-atoms.button
                                                    class="btn-secondary position-relative"
                                                    type="submit"
                                                    title="Adicionar papel"
                                                >
                                                    <i
                                                        class="bi bi-cart-plus"
                                                    ></i>
                                                </x-atoms.button>
                                            </form>
                                        </div>
                                    </li>
                                    <li>
                                        <x-atoms.button
                                            format="anchor"
                                            class="btn-secondary"
                                            href="{{ route('roles.edit', ['role' => $role->id]) }}"
                                            title="Editar"
                                        >
                                            <i class="bi bi-wrench"></i>
                                        </x-atoms.button>
                                    </li>
                                    <li>
                                        <x-organisms.confirm-rm-btn
                                            :routeParams="['role' => $role->id]"
                                            route="roles.destroy"
                                            heading="Remover este papel?"
                                            positiveText="Remover papel"
                                            title="Remover papel"
                                        >
                                            Isso removerá permanentemente este
                                            papel.
                                        </x-organisms.confirm-rm-btn>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td
                                colspan="4"
                                class="no-values"
                            >
                                Sem papeis para o filtro atual
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-molecules.table-index>
            <x-molecules.root-pagination :paginator="$list" />
        </section>
        <x-packs.toast />
    </main>
</x-layout>
