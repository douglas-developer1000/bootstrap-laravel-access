@php
    /**
     * @see App\View\Components\Organisms\ConfirmBtn::class
     **/
@endphp
<x-atoms.button
    @class($btnCssClasses)
    data-bs-toggle="modal"
    data-bs-target="#confirmModal{{ $id }}"
    title="{{ $title }}"
    :dataset="$btnDataset"
    :disabled="$btnDisabled"
>
    @if (isset($btnContent))
        {{ $btnContent }}
    @else
        <i class="bi {{ $icon }}"></i>
    @endif
</x-atoms.button>
<x-molecules.confirm-modal
    :id="$id"
    :method="$method"
    :heading="$heading"
    :negativeText="$negativeText"
    :positiveText="$positiveText"
    :href="$href"
    :formId="$formId"
>
    {{ $slot }}
</x-molecules.confirm-modal>
