<!DOCTYPE html>
<html lang="pt-BR" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erro interno — UniAnimal</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        body { display: flex; align-items: center; justify-content: center; min-height: 100vh; text-align: center; padding: 2rem; background: var(--bg); color: var(--txt); font-family: var(--f-ui); }
        .error-box { max-width: 520px; }
        .error-icon { font-size: 5rem; }
        .error-title { font-size: 1.6rem; margin: 1rem 0 0.5rem; color: var(--txt); }
        .error-msg { color: var(--txt-2); margin-bottom: 2rem; line-height: 1.6; }
        .btn-back { display: inline-block; padding: .75rem 2rem; background: var(--t); color: #0D1B2A; border-radius: 8px; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
    <div class="error-box">
        <div class="error-icon">⚠️</div>
        <h1 class="error-title">Serviço temporariamente indisponível</h1>
        <p class="error-msg">{{ $message ?? 'Ocorreu um erro interno. Nossa equipe foi notificada e está trabalhando para resolver o problema.' }}</p>
        <a href="{{ url()->previous() ?: route('home') }}" class="btn-back">← Voltar</a>
    </div>
</body>
</html>
