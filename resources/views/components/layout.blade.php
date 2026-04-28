<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    />
    <meta
        http-equiv="X-UA-Compatible"
        content="IE=edge"
    />
    <meta
        name="description"
        content="{{ $metaDescription ?? 'Site particular "organizavenda"' }}"
    />
    <meta
        name="author"
        content="Douglas Leandro, douglas.developer1000@gmail.com"
    />

    <title>{{ $title ?? '' }}</title>

    <link
        rel="icon"
        href="/images/favicon/favicon-16x16.png"
        type="image/png"
        sizes="16x16"
    />
    <link
        rel="icon"
        href="/images/favicon/favicon-32x32.png"
        type="image/png"
        sizes="32x32"
    />

    @foreach ([60, 72, 76, 114, 120, 144, 152, 167, 180] as $size)
        <link
            rel="apple-touch-icon"
            href="/images/favicon/apple/apple-touch-icon-{{ "{$size}x{$size}" }}.png"
            sizes="{{ "{$size}x{$size}" }}"
        />
    @endforeach

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    />
    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    />
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Mitr:wght@200;300;400;500;600;700&display=swap"
        rel="stylesheet"
    />

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
        crossorigin="anonymous"
    />

    <!-- Global styles / Global scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite (['resources/css/app.css', 'resources/js/app.ts'])
    @endif
    @stack ('ecmascript-top')
    @stack ('styling')
</head>
<body>
    {{ $slot }}

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"
    ></script>

    @stack ('ecmascript-bottom')
</body>
</html>
