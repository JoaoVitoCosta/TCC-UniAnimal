<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'ensure.tutor' => \App\Http\Middleware\EnsureTutorExists::class,
            'api.token'    => \App\Http\Middleware\EnsureApiToken::class,
            'iot.coleira'  => \App\Http\Middleware\EnsureDeviceKey::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // QueryException — erros de banco de dados
        $exceptions->render(function (QueryException $e, $request) {
            Log::error('QueryException: ' . $e->getMessage(), [
                'sql'      => $e->getSql() ?? '',
                'bindings' => $e->getBindings() ?? [],
                'url'      => $request->fullUrl(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'error'   => 'Erro interno no servidor.',
                    'message' => 'Não foi possível processar a requisição.',
                ], 500);
            }

            return response()->view('errors.database', [
                'message' => 'Ocorreu um erro ao acessar o banco de dados. Nossa equipe foi notificada.',
            ], 500);
        });

        // 404 — rota não encontrada
        $exceptions->render(function (NotFoundHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Recurso não encontrado.'], 404);
            }
            return response()->view('errors.404', [], 404);
        });

        // Outros erros HTTP
        $exceptions->render(function (HttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => $e->getMessage() ?: 'Erro na requisição.',
                ], $e->getStatusCode());
            }

            $view = view()->exists("errors.{$e->getStatusCode()}")
                ? "errors.{$e->getStatusCode()}"
                : 'errors.generic';

            return response()->view($view, ['exception' => $e], $e->getStatusCode());
        });

    })->create();
