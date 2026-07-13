@use ('App\Libraries\Enums\LicenseStatusEnum')
@use ('App\Libraries\Utils\DatetimeFormatter')
@push ('styling')
    @vite ([
        'resources/css/pages/generic/default.css',
        'resources/css/pages/generic/table.css',
        'resources/css/pages/generic/show.css',
    ])
@endpush
@push ('ecmascript-bottom')
    @vite ([
        'resources/js/pages/generic/poli-multiselection.ts'
    ])
@endpush

<x-layout title="Visualizar Usuário">
    <x-packs.header>
        <x-packs.page-heading-row>
            <x-slot:heading>
                Visualizar Usuário:
                <span class="text-primary ms-2">{{ $user->name }}</span>
            </x-slot:heading>
            <x-molecules.impersonate-login-btn :id="$user->id" />
        </x-packs.page-heading-row>
    </x-packs.header>
    <main class="bg-secondary-subtle create-main main-default">
        <section class="content bg-light d-flex flex-column row-gap-3">
            <fieldset
                class="border border-1 border-dark rounded-1 fieldset-tag"
            >
                <legend class="field-legend bg-light">Dados</legend>
                <div class="data-box" style="--second-column-size: 1fr">
                    <div class="label">Nome:</div>
                    <div class="ellipsis">{{ $user->name }}</div>
                    <div class="label">E-mail:</div>
                    <div class="ellipsis">{{ $user->email ?? 'N/A' }}</div>
                    <div class="label">WhatsApp:</div>
                    <div>
                        @if ($user->phone->getValue())
                            <a href="{{ 'https://wa.me/55' . $user->phone->getValue() }}" class="text-truncate text-decoration-none text-info border-0 ps-0">
                                {{ $user->phone }}
                            </a>
                        @else
                            {{ $user->phone }}
                        @endif
                    </div>
                    <div class="label">Telefone:</div>
                    <div class="ellipsis">
                        @if ($user->phone->getValue())
                            <a href="{{ 'tel:+55' . $user->phone->getValue() }}" class="text-truncate text-decoration-none text-info border-0 ps-0">
                                {{ $user->phone }}
                            </a>
                        @else
                            {{ $user->phone }}
                        @endif
                    </div>
                </div>
            </fieldset>
            <fieldset
                class="border border-1 border-dark rounded-1 fieldset-tag"
            >
                <legend class="field-legend bg-light">Licenças</legend>
                @forelse ($licenses as $license)
                    <div class="accordion accordion-flush" id="accordion-licenses">
                        <div class="accordion-item">
                            <div class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse-license-{{ $license->id }}" aria-expanded="false" aria-controls="flush-collapse-license-{{ $license->id }}">
                                    {{ $license->plan->name }}
                                    @if ($license->status === LicenseStatusEnum::ACTIVE)
                                        <span class="ms-1 text-success">(ativo)</span>
                                    @endif
                                    @if ($license->status === LicenseStatusEnum::PENDING)
                                        <span class="ms-1 text-danger">(pendente)</span>
                                    @endif
                                </button>
                            </div>
                            <div id="flush-collapse-license-{{ $license->id }}" class="accordion-collapse collapse" data-bs-parent="#accordion-licenses">
                                <div class="accordion-body">
                                    <table class="table tabular-data">
                                        <tbody>
                                            <tr>
                                                <th scope="row" class="text-end align-middle">Status:</th>
                                                <td class="text-start align-middle">{{$license->status->toString()}}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row" class="text-end align-middle">Faturamento:</th>
                                                <td class="text-start align-middle">{{$license->plan->billing_period->toString()}}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row" class="text-end align-middle">Preço pago:</th>
                                                <td class="text-start align-middle">{{$parsePrice($license->price_paid)}}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row" class="text-end align-middle">Inicia em:</th>
                                                <td class="text-start align-middle">{{DatetimeFormatter::formatToDate($license->starts_at)}}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row" class="text-end align-middle">Expira em:</th>
                                                <td class="text-start align-middle">{{DatetimeFormatter::formatToDate($license->expires_at)}}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row" class="text-end align-middle">Recorrente:</th>
                                                <td class="text-start align-middle">{{$license->is_recurring ? 'Sim' : 'Não'}}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row" class="text-end align-top pt-4">Recursos Adicionais:</th>
                                                <td class="text-start align-middle">
                                                    <ul class="list-group">
                                                        @forelse ($license->additionals ?? [] as $additional)
                                                            <li class="list-group-item">
                                                                {{ $additional->name }}
                                                            </li>
                                                        @empty
                                                            <li class="list-group-item text-danger">Sem Adicionais</li>
                                                        @endforelse
                                                    </ul>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row" class="text-end align-top pt-4">Ações:</th>
                                                <td class="text-start align-middle no-values">
                                                    <x-organisms.confirm-cancel-btn
                                                        :routeParams="['license' => $license->id]"
                                                        route="licenses.cancel"
                                                        heading="Cancelar esta licença?"
                                                        positiveText="Cancelar licença"
                                                        title="Cancelar licença"
                                                        :disabled="!$license->isPreCancellable && !$license->isPostCancellable"
                                                    >
                                                        Essa licença mudará seu status de 
                                                        "{{
                                                            $license->status->toString()
                                                        }}" para "{{
                                                            LicenseStatusEnum::CANCELED->toString()
                                                        }}".
                                                    </x-organisms.confirm-cancel-btn>
                                                    <x-organisms.confirm-activate-btn
                                                        :routeParams="['license' => $license->id]"
                                                        route="licenses.activate"
                                                        heading="Ativar esta licença?"
                                                        positiveText="Ativar licença"
                                                        negativeText="Ainda não"
                                                        title="Ativar licença"
                                                        :disabled="!$license->isActivatable && !$license->isReactivatable"
                                                    >
                                                        Essa licença mudará seu status de 
                                                        "{{
                                                            $license->status->toString()
                                                        }}" para "{{
                                                            LicenseStatusEnum::ACTIVE->toString()
                                                        }}".
                                                    </x-organisms.confirm-activate-btn>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                
                @empty
                    <ul class="list-group">
                        <li class="list-group-item text-danger">Nenhuma licença</li>
                    </ul>
                @endforelse
            </fieldset>
            <fieldset
                class="border border-1 border-dark rounded-1 fieldset-tag"
            >
                <legend class="field-legend bg-light">Papéis</legend>
                <div class="fieldset-top-btn">
                    <x-organisms.confirm-detach-group-btn
                        :routeParams="['user' => $user->id]"
                        route="users.unbind.roles.group"
                        heading="Desvincular estes papéis?"
                        positive-text="Desvincular papéis"
                        title="Desvincular papéis selecionados"
                        :dataset="[['key' => 'key', 'value' => 'roles']]"
                    >
                        Isso desvinculará os papéis selecionados do usuário {{ $user->name }}.
                    </x-organisms.confirm-detach-group-btn>
                    <x-atoms.button
                        class="btn-secondary"
                        format="anchor"
                        href="{{ route('users.attach.roles', ['user' => $user->id]) }}"
                        title="Vincular papel"
                    >
                        <i class="bi bi-plus h-1 icon-s2"></i>
                    </x-atoms.button>
                </div>
                <table class="table tabular-data">
                    <thead>
                        <tr>
                            <th
                                scope="col"
                                class="with-checker"
                            >
                                <input
                                    type="checkbox"
                                    class="form-check-input cursor-pointer multiselection-all"
                                    data-key="roles"
                                />
                            </th>
                            <th scope="col">Nome</th>
                            <th
                                scope="col"
                                class="last-thdata"
                                style="width: 4em"
                            >
                                Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($roles as $role)
                            <tr>
                                <td>
                                    <input
                                        type="checkbox"
                                        value="{{ $role->id }}"
                                        class="form-check-input cursor-pointer multiselection-item"
                                        data-key="roles"
                                    />
                                </td>
                                <td>{{$role->name}}</td>
                                <td>
                                    <div
                                        class="w-100 d-flex justify-content-between gap-1"
                                    >
                                        <x-organisms.confirm-detach-btn
                                            :routeParams="['user' => $user->id, 'role' => $role->id]"
                                            route="users.unbind.roles"
                                            heading="Desvincular este papel?"
                                            negative-text="Agora não"
                                            positive-text="Desvincular papel"
                                            title="Desvincular"
                                        >
                                            Isso desvinculará o papel
                                            <span
                                                class="fw-medium"
                                                >{{ $role->name }}</span
                                            >
                                            do usuário
                                            <span
                                                class="fw-medium"
                                                >{{ $user->name }}</span
                                            >.
                                        </x-organisms.confirm-detach-btn>
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
            <fieldset
                class="border border-1 border-dark rounded-1 fieldset-tag"
            >
                <legend class="field-legend bg-light">Permissões</legend>
                <table class="table tabular-data">
                    <thead>
                        <tr>
                            <th scope="col">Nome</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($permissions as $perm)
                            <tr>
                                <td>{{$perm->name}}</td>
                            </tr>
                        @empty
                            <tr>
                                <td
                                    colspan="1"
                                    class="no-values"
                                >
                                    Sem permissões vinculadas
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </fieldset>
            <fieldset
                class="border border-1 border-dark rounded-1 fieldset-tag"
            >
                <legend class="field-legend bg-light">
                    Permissões diretas
                </legend>
                <div class="fieldset-top-btn">
                    <x-organisms.confirm-detach-group-btn
                        :routeParams="['user' => $user->id]"
                        route="users.unbind.permissions.group"
                        heading="Desvincular estas permissões?"
                        positive-text="Desvincular permissões"
                        title="Desvincular papéis selecionados"
                        :dataset="[['key' => 'key', 'value' => 'direct-permissions']]"
                    >
                        Isso disvinculará as permissões diretas selecionadas do
                        usuário {{ $user->name }}.
                    </x-organisms.confirm-detach-group-btn>
                    <x-atoms.button
                        class="btn-secondary"
                        format="anchor"
                        href="{{ route('users.attach.permissions', ['user' => $user->id]) }}"
                        title="Vincular permissão direta"
                    >
                        <i class="bi bi-plus h-1 icon-s2"></i>
                    </x-atoms.button>
                </div>
                <table class="table tabular-data">
                    <thead>
                        <tr>
                            <th scope="col">
                                <input
                                    type="checkbox"
                                    class="form-check-input cursor-pointer multiselection-all"
                                    data-key="direct-permissions"
                                />
                            </th>
                            <th scope="col">Nome</th>
                            <th
                                scope="col"
                                class="last-thdata"
                                style="width: 4em"
                            >
                                Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($dPermissions as $perm)
                            <tr>
                                <td>
                                    <input
                                        type="checkbox"
                                        value="{{ $perm->id }}"
                                        class="form-check-input cursor-pointer multiselection-item"
                                        data-key="direct-permissions"
                                    />
                                </td>
                                <td>{{$perm->name}}</td>
                                <td>
                                    <div
                                        class="w-100 d-flex justify-content-between gap-1"
                                    >
                                        <x-organisms.confirm-detach-btn
                                            :routeParams="['user' => $user->id, 'permission' => $perm->id]"
                                            route="users.unbind.permissions"
                                            heading="Desvincular esta permissão?"
                                            negative-text="Agora não"
                                            positive-text="Desvincular permissão"
                                            title="Desvincular"
                                        >
                                            Isso desvinculará a permissão
                                            <span
                                                class="fw-medium"
                                                >{{ $perm->name }}</span
                                            >
                                            do usuário
                                            <span
                                                class="fw-medium"
                                                >{{ $user->name }}</span
                                            >.
                                        </x-organisms.confirm-detach-btn>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td
                                    colspan="3"
                                    class="no-values"
                                >
                                    Sem permissões diretas vinculadas
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </fieldset>
        </section>
        <x-packs.toast />
    </main>
</x-layout>
