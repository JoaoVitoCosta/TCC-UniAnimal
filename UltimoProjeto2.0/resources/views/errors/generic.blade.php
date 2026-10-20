<!DOCTYPE html>
<html lang="pt-BR" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erro — UniAnimal</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        body { display: flex; align-items: center; justify-content: center; min-height: 100vh; text-align: center; padding: 2rem; background: var(--bg); color: var(--txt); font-family: var(--f-ui); }
        .error-box { max-width: 480px; }
        .error-title { font-size: 1.6rem; margin: 1rem 0 0.5rem; color: var(--txt); }
        .error-msg { color: var(--txt-2); margin-bottom: 2rem; }
        .btn-back { display: inline-block; padding: .75rem 2rem; background: var(--t); color: #0D1B2A; border-radius: 8px; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
    <div class="error-box">
        <div style="font-size:5rem">❌</div>
        <h1 class="error-title">Algo deu errado</h1>
        <p class="error-msg">Ocorreu um erro inesperado. Por favor, tente novamente.</p>
        <a href="{{ url()->previous() ?: route('home') }}" class="btn-back">← Voltar</a>
    </div>
</body>
</html>
