<div {{
    $attributes->class([$spacing === 'bottom' ? 'mb-3' : 'mt-3'])
}}>
    <nav class="d-flex justify-items-center justify-content-between">
        <div class="d-flex justify-content-between flex-fill d-sm-none">
            <ul class="pagination">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <li
                        class="page-item disabled"
                        aria-disabled="true"
                    >
                        <span class="page-link"> Anterior </span>
                    </li>
                @else
                    <li class="page-item">
                        <a
                            class="page-link"
                            href="{{ $makeHref($paginator->previousPageUrl()) }}"
                            rel="prev"
                        >
                            Anterior
                        </a>
                    </li>
                @endif

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a
                            class="page-link"
                            href="{{ $makeHref($paginator->nextPageUrl()) }}"
                            rel="next"
                        >
                            Próximo
                        </a>
                    </li>
                @else
                    <li
                        class="page-item disabled"
                        aria-disabled="true"
                    >
                        <span class="page-link">Próximo</span>
                    </li>
                @endif
            </ul>
        </div>

        <div
            class="d-none flex-sm-fill d-sm-flex align-items-sm-center justify-content-sm-between flex-wrap gap-2"
        >
            <div class="small text-muted w-100 text-center">
                <span>Mostrando de</span>
                <span
                    class="fw-semibold"
                    >{{ $paginator->firstItem() ?? 0 }}</span
                >
                <span>a</span>
                <span class="fw-semibold">{{ $paginator->lastItem() }}</span>
                <span>de</span>
                <span class="fw-semibold">{{ $paginator->total() }}</span>
                <span>resultados</span>
            </div>
            <div class="d-flex w-100 flex-wrap justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <div style="min-width: 4rem">Páginas</div>
                    <ul class="pagination mb-0">
                        {{-- Previous Page Link --}}
                        @if ($paginator->onFirstPage())
                            <li
                                class="page-item disabled"
                                aria-disabled="true"
                                aria-label="Anterior"
                            >
                                <span
                                    class="page-link"
                                    aria-hidden="true"
                                    >&lsaquo;</span
                                >
                            </li>
                        @else
                            <li class="page-item">
                                <a
                                    class="page-link"
                                    href="{{ $makeHref($paginator->previousPageUrl()) }}"
                                    rel="prev"
                                    aria-label="Anterior"
                                    >&lsaquo;</a
                                >
                            </li>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach ($elements as $element)
                            {{-- "Three Dots" Separator --}}
                            @if (is_string($element))
                                <li
                                    class="page-item disabled"
                                    aria-disabled="true"
                                >
                                    <span
                                        class="page-link"
                                        >{{ $element }}</span
                                    >
                                </li>
                            @endif
                            {{-- Array Of Links --}}
                            @if (is_array($element))
                                @foreach ($element as $page => $url)
                                    @if ($page == $paginator->currentPage())
                                        <li
                                            class="page-item active"
                                            aria-current="page"
                                        >
                                            <span
                                                class="page-link"
                                                >{{ $page }}</span
                                            >
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a
                                                class="page-link"
                                                href="{{ $makeHref($url) }}"
                                                >{{ $page }}</a
                                            >
                                        </li>
                                    @endif
                                @endforeach
                            @endif
                        @endforeach

                        {{-- Next Page Link --}}
                        @if ($paginator->hasMorePages())
                            <li class="page-item">
                                <a
                                    class="page-link"
                                    href="{{ $makeHref($paginator->nextPageUrl()) }}"
                                    rel="next"
                                    aria-label="Próximo"
                                    >&rsaquo;</a
                                >
                            </li>
                        @else
                            <li
                                class="page-item disabled"
                                aria-disabled="true"
                                aria-label="Próximo"
                            >
                                <span
                                    class="page-link"
                                    aria-hidden="true"
                                    >&rsaquo;</span
                                >
                            </li>
                        @endif
                    </ul>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div style="min-width: 4rem">Grupos</div>
                    <nav aria-label="navegação por grupos">
                        <ul class="pagination mb-0">
                            @foreach (config('pagination.groups') as $group)
                                @if ($group == $groupSelected)
                                    <li class="page-item active">
                                        <span
                                            class="page-link"
                                            aria-current="page"
                                            >{{ $group }}</span
                                        >
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a
                                            class="page-link"
                                            href="{{ $makeHref(request()->url(), $group) }}"
                                            >{{ $group }}</a
                                        >
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </nav>
</div>
