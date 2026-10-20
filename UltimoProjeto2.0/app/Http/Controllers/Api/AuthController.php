<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApiToken;
use App\Models\Coleira;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * Login do aplicativo Flutter pelo código único da coleira.
 *
 * Fluxo real (nada simulado no cliente):
 *   Flutter -> POST /api/v1/auth/coleira { codigo }
 *   -> valida existência e vínculo no banco
 *   -> emite token opaco persistido (hash) em api_tokens
 *   -> Flutter usa o token em Authorization: Bearer <token>
 */
class AuthController extends Controller
{
    public function loginPorColeira(Request $request)
    {
        $data = $request->validate([
            'codigo' => ['required', 'string', 'max:50'],
            'device_name' => ['nullable', 'string', 'max:150'],
        ], [
            'codigo.required' => 'Digite o código da coleira.',
        ]);

        $codigo = trim($data['codigo']);

        $coleira = Coleira::where('codigo', $codigo)->first();

        // Código inexistente: nunca revelar se é "errado" ou "não vinculado"
        // com detalhe demais — mensagem única evita enumeração de códigos.
        if (! $coleira) {
            throw ValidationException::withMessages([
                'codigo' => 'Código inválido. Verifique o código impresso na coleira.',
            ]);
        }

        if ($coleira->status === 'inativa') {
            throw ValidationException::withMessages([
                'codigo' => 'Esta coleira está inativa. Entre em contato com o suporte.',
            ]);
        }

        if ($coleira->estaDisponivel()) {
            // Coleira existe no estoque mas ainda não foi vinculada a
            // nenhum animal — não há conta para autenticar ainda.
            throw ValidationException::withMessages([
                'codigo' => 'Esta coleira ainda não foi vinculada a um animal. '
                    . 'Cadastre o animal no site/app usando este código primeiro.',
            ]);
        }

        $animal = $coleira->animal()->with('tutor.user')->first();

        if (! $animal || ! $animal->tutor) {
            Log::warning('Coleira vinculada sem tutor consistente.', ['coleira_id' => $coleira->id]);
            throw ValidationException::withMessages([
                'codigo' => 'Não foi possível autenticar com este código. Tente novamente mais tarde.',
            ]);
        }

        [$plainToken, $tokenRecord] = ApiToken::issueFor(
            tutor: $animal->tutor,
            coleira: $coleira,
            deviceName: $data['device_name'] ?? $request->userAgent(),
        );

        return response()->json([
            'token'      => $plainToken,
            'expires_at' => $tokenRecord->expires_at,
            'tutor'      => [
                'id'    => $animal->tutor->id,
                'nome'  => $animal->tutor->nome,
                'email' => $animal->tutor->email,
            ],
            'animal' => [
                'id'      => $animal->id,
                'nome'    => $animal->nome,
                'especie' => $animal->especie,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $apiToken = $request->attributes->get('api_token');
        $apiToken?->delete();

        return response()->json(['message' => 'Sessão encerrada.']);
    }
}
