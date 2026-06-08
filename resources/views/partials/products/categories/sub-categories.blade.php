<ul class="mw-100 overflow-x-auto mb-0 ps-3">
    @foreach ($categories as $category)
        <li>
            {{ $category->name }}
            @php
                $children = $category->children;
            @endphp
            @if ($children)
                @include ('partials.products.categories.sub-categories', [
                    'categories' => $children
                ])
            @endif
        </li>
    @endforeach
</ul>
