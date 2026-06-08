@props (['cols' => NULL, 'styleRows' => [], 'qtyBtns' => 2])

@php
    $styles = collect($styleRows);
@endphp

<table class="table table-hover table-striped list-table tabular-data">
    <colgroup class="table-colgroup">
        <col
            class="col-first"
            span="1"
            @if ($styles->has('first'))
                style="{{ $styles->get('first') }}"
            @endif
        />
        <col
            class="col-second"
            span="1"
            @if ($styles->has('second'))
                style="{{ $styles->get('second') }}"
            @endif
        />
        @if ($cols)
            {{ $cols }}
        @endif
        <col
            class="col-last"
            style="--qty-btn: {{ $qtyBtns }};"
        />
    </colgroup>

    {{ $slot }}
</table>
