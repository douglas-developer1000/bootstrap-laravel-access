@props ([
    /** @var 'btn'|'anchor'|null format **/
    'format' => NULL
])

@switch ($format)
    @case ('anchor')
        <a {{ $attributes->class(['btn']) }}> {{ $slot }} </a>
        @break
    @default
        <button {{ $attributes->class(['btn'])->merge(['type' => 'button']) }}>
            {{ $slot }}
        </button>
@endswitch
