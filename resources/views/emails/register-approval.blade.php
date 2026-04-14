<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Conta criada com sucesso</title>
</head>
<body>
    @if ($logo ?? FALSE)
        <img
            src="{{ $logo }}"
            alt="Logotipo"
            style="border-radius: 50%"
        />
    @endif
    <h2>Parabéns!</h2>
    <p>A partir de agora, você poderá registrar sua nova conta.</p>
    <p>Para começar a usar, primeiro cadastre seus dados clicando no botão abaixo:</p>
    <p>
        <a
            href="{{ $url }}"
            target="_blank"
            style="
                display: inline-block;
                padding: 10px 20px;
                background-color: #3490dc;
                color: #fff;
                text-decoration: none;
                border-radius: 0.5rem;
            "
        >
            Clique aqui
        </a>
    </p>

    <p>Atenciosamente,<br />
    {{ config('app.name') }}</p>
</body>
</html>
