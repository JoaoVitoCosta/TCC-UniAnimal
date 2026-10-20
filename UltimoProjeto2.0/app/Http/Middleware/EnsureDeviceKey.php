<?php

namespace App\Http\Middleware;

use App\Models\Coleira;
use App\Models\Telemetria;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Autenticação do HARDWARE (coleira) na API IoT.
 *
 *   Authorization: Bearer <chave da coleira>
 *
 * A chave é gerada por "php artisan coleira:chave CODIGO" e gravada no
 * firmware. Só coleiras vinculadas a um animal e não inativas transmitem.
 * Tentativas recusadas também ficam registradas em "telemetrias".
 */
class EnsureDeviceKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $chave = (string) $request->bearerToken();
        $coleira = Coleira::porChave($chave);

        if (! $coleira) {
            return $this->recusar($request, null, 'Chave do dispositivo ausente ou inválida.', 401);
        }

        if ($coleira->status === 'inativa') {
            return $this->recusar($request, $coleira, 'Coleira inativa. Envio bloqueado.', 403);
        }

        if (! $coleira->animal_id) {
            return $this->recusar($request, $coleira, 'Coleira ainda não vinculada a um animal.', 403);
        }

        $request->attributes->set('coleira', $coleira->load('animal'));

        return $next($request);
    }

    private function recusar(Request $request, ?Coleira $coleira, string $erro, int $status): Response
    {
        Telemetria::create([
            'coleira_id'  => $coleira?->id,
            'payload'     => $request->json()->all() ?: ['_sem_corpo' => true],
            'ip'          => $request->ip(),
            'situacao'    => 'rejeitada',
            'erro'        => $erro,
            'recebido_em' => now(),
        ]);

        return response()->json(['message' => $erro], $status);
    }
}
