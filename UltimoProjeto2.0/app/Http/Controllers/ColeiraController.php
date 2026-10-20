<?php

namespace App\Http\Controllers;

use App\Http\Resources\ColeiraResource;
use App\Http\Resources\LocalizacaoResource;
use App\Models\Animal;
use App\Models\Coleira;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ColeiraController extends Controller
{
    /**
     * Tela "Localização da Coleira" com mapa, última posição e histórico.
     */
    public function show(Animal $animal)
    {
        Gate::authorize('view', $animal);

        $animal->load(['coleira.localizacoes' => fn ($q) => $q->take(50)]);

        return view('pages.coleira.show', compact('animal'));
    }

    /**
     * Histórico de posições em JSON (para o mapa/gráfico).
     */
    public function historico(Animal $animal)
    {
        Gate::authorize('view', $animal);

        $localizacoes = $animal->coleira
            ?->localizacoes()
            ->take(50)
            ->get(['latitude', 'longitude', 'precisao', 'registrado_em'])
            ?? collect();

        return LocalizacaoResource::collection($localizacoes->reverse()->values());
    }

    /**
     * Endpoint de demonstração: gera uma posição/bateria simuladas.
     * NÃO representa hardware real — apenas prepara a arquitetura para
     * uma futura integração de IoT/GPS em tempo real.
     */
    public function simular(Animal $animal)
    {
        Gate::authorize('view', $animal);

        $coleira = $animal->coleira;

        if (! $coleira) {
            return response()->json(['error' => 'Este animal não possui coleira cadastrada.'], 422);
        }

        // Pequena variação em torno da última posição conhecida (ou um ponto padrão).
        $latBase = $coleira->latitude ?? -23.5505;
        $lngBase = $coleira->longitude ?? -46.6333;

        $latitude  = $latBase + (rand(-50, 50) / 100000);
        $longitude = $lngBase + (rand(-50, 50) / 100000);
        $precisao  = rand(3, 15);
        $bateria = max(0, ($coleira->bateria_percentual ?? 100) - rand(0, 1));
        $bateriaStatus = match (true) {
            $bateria <= 15 => 'critica',
            $bateria <= 35 => 'baixa',
            default        => 'normal',
        };

        $coleira->update([
            'latitude'                  => $latitude,
            'longitude'                 => $longitude,
            'precisao'                  => $precisao,
            'localizacao_atualizada_em' => now(),
            'bateria_percentual'        => $bateria,
            'bateria_status'            => $bateriaStatus,
            'bateria_atualizada_em'     => now(),
            'status'                    => 'ativa',
        ]);

        $coleira->localizacoes()->create([
            'latitude'      => $latitude,
            'longitude'     => $longitude,
            'precisao'      => $precisao,
            'registrado_em' => now(),
        ]);

        return response()->json([
            'coleira'   => new ColeiraResource($coleira->fresh()),
            'timestamp' => now()->format('H:i:s'),
        ]);
    }
}
