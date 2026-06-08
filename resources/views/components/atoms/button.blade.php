@props ([
    /** @var 'btn'|'anchor'|null format **/
    'format' => NULL,
    'disabled' => FALSE,
    'dataset' => [],
])

@switch ($format)
    @case ('anchor')
        <a
            {{ $attributes->class([
                'btn',
                'disabled' => $disabled
            ]) }}
            @foreach ($dataset as $data)
                data-{{ $data['key'] }}="{{ $data['value'] }}"
            @endforeach
            @if ($disabled)
                tab-index="-1"
                aria-disabled="true"
            @endif
        >
            {{ $slot }}
        </a>
        @break
    @default
        <button
            {{ $attributes->class(['btn'])->merge(['type' => 'button']) }}
            @foreach ($dataset as $data)
                data-{{ $data['key'] }}="{{ $data['value'] }}"
            @endforeach
            @disabled ($disabled)
            @if ($disabled)
                aria-disabled="true"
            @endif
        >
            {{ $slot }}
        </button>
@endswitch
