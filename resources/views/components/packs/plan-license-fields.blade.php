@push('styling')
    @vite ([
        'resources/css/components/packs/plan-license-fields.blade.css',
    ])
@endpush
@push ('ecmascript-bottom')
    @vite ([
        'resources/js/components/packs/plan-license-fields.ts',
    ])
@endpush

<div class="d-flex align-items-top column-gap-3 justify-content-start">
    <x-molecules.select-field
        class="w-auto"
        label-text="{{ $label }}"
        placeholder="Selecione..."
        aria-label="{{ $phrase }}"
        name="plan"
        size="auto"
        :required="$required"
        :value="old('plan', $planId)"
    >
        @foreach ($plans as $planItem)
            <option @selected (old('plan') === $planItem->slug) value="{{ $planItem->slug }}">
                {{ $planItem->name }}
            </option>
        @endforeach
    </x-molecules.select-field>
    <div class="d-flex gap-2 mt-4">
        <x-molecules.input-check class-label="fs-075" name="recurring" checked="{{ old('recurring', $recurring) }}">
            Plano recorrente
        </x-molecules.input-check>
    </div>
</div>
<div class="form-label px-0 fs-075">Recursos adicionais:</div>
<div class="additional-placeholder border border-secondary rounded p-1">{{ $phrase }}</div>
@foreach ($additionalRoles as $slug => $roles)
    <ul class="list-group" data-slug="{{ $slug }}">
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
                            ['initial' => false, 'old' => old('additionals', $additionals)],
                            fn(string $name) => $name === $role->name
                        )
                    ) />
                <label for="{{ $role->name }}">{{ $role->name }}</label>
            </li>
        @empty
            <li class="list-group-item text-danger">
                Sem recursos adicionais
            </li>
        @endforelse
    </ul>
@endforeach