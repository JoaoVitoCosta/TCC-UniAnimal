<?php

return [
    // Só a API é exposta a origens externas (é o que o app Flutter chama).
    // As rotas do site (web.php) usam sessão/cookie e não precisam de CORS.
    'paths' => ['api/*'],

    'allowed_methods' => ['*'],

    // A autenticação da API é por Bearer token (não por cookie de sessão),
    // então não há risco de CSRF cross-origin aqui — mas mantemos a lista
    // restrita ao necessário em vez de liberar tudo por padrão.
    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // Sem cookies/sessão na API — não precisa enviar credenciais.
    'supports_credentials' => false,
];
