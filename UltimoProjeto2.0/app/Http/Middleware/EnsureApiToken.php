<?php

namespace App\Http\Middleware;

use App\Models\ApiToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Autenticação real do aplicativo Flutter.
 *
 * Cada requisição autenticada precisa do header:
 *   Authorization: Bearer <token>
 *
 * O token é validado contra o banco (api_tokens), nunca aceito por
 * inspeção local no app. Se válido, o tutor autenticado fica disponível
 * em $request->attributes->get('tutor') e via Auth::user() (usuário
 * dono do tutor), para reaproveitar Policies/regras já existentes.
 */
class EnsureApiToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $header = $request->header('Authorization', '');

        if (! str_starts_with($header, 'Bearer ')) {
            return response()->json([
                'message' => 'Não autenticado. Faça login com o código da coleira novamente.',
            ], 401);
        }

        $plainText = substr($header, 7);
        $apiToken = ApiToken::findValidByPlainText($plainText);

        if (! $apiToken) {
            return response()->json([
                'message' => 'Sessão expirada ou inválida. Faça login novamente.',
            ], 401);
        }

        $tutor = $apiToken->tutor()->with('user')->first();

        if (! $tutor) {
            return response()->json(['message' => 'Conta não encontrada.'], 401);
        }

        $apiToken->markUsedNow();

        $request->attributes->set('api_token', $apiToken);
        $request->attributes->set('tutor', $tutor);
        auth()->setUser($tutor->user);

        return $next($request);
    }
}
