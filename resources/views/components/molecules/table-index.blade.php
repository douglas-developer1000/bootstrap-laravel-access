@props (['cols' => NULL, 'newFirstCol' => NULL])

<table class="table table-hover table-striped list-table tabular-data">
    @if ($cols)
        <colgroup class="table-colgroup">
            {{ $newFirstCol ?? '' }}
            <col
                class="col-first @if($newFirstCol) visibility-collapsed @endif"
                span="1"
            />
            <col
                class="col-second"
                span="1"
            />
            {{ $cols }}
            <col class="col-last" />
        </colgroup>
    @endif

    {{ $slot }}
</table>
