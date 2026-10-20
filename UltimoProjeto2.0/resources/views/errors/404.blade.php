<!DOCTYPE html>
<html lang="pt-BR" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página não encontrada — UniAnimal</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        body { display: flex; align-items: center; justify-content: center; min-height: 100vh; text-align: center; padding: 2rem; background: var(--bg); color: var(--txt); font-family: var(--f-ui); }
        .error-box { max-width: 480px; }
        .error-code { font-size: 7rem; font-weight: 800; color: var(--t); line-height: 1; }
        .error-title { font-size: 1.5rem; margin: 1rem 0 0.5rem; color: var(--txt); }
        .error-msg { color: var(--txt-2); margin-bottom: 2rem; }
        .btn-home { display: inline-block; padding: .75rem 2rem; background: var(--t); color: #0D1B2A; border-radius: 8px; text-decoration: none; font-weight: 600; }
        .btn-home:hover { opacity: .88; }
    </style>
</head>
<body>
    <div class="error-box">
        <div class="error-code">404</div>
        <h1 class="error-title">Página não encontrada</h1>
        <p class="error-msg">A página que você procura não existe ou foi movida.</p>
        <a href="{{ route('home') }}" class="btn-home">🏠 Voltar ao início</a>
    </div>
</body>
</html>
