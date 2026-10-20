<?php

namespace App\Services;

use App\Models\Alerta;
use App\Models\Animal;
use App\Models\Coleira;
use App\Models\LeituraSaude;
use App\Models\Localizacao;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Processa um envio de telemetria da coleira (API IoT):
 *
 *   1. grava os sinais vitais (leituras_saude) e a posição (localizacoes);
 *   2. atualiza o estado da coleira (posição, bateria, firmware, último contato);
 *   3. avalia batimento, temperatura, bateria e zona segura → abre ou
 *      resolve alertas e atualiza o status do animal.
 *
 * Todos os campos do envio são opcionais: o firmware pode mandar só o que
 * mediu naquele ciclo (ex.: GPS a cada 5 min e batimento a cada 30 s).
 */
class TelemetriaService
{
    public function __construct(private AlertaService $alertas) {}

    /**
     * @param  array  $dados  campos já validados (ver TelemetriaController::REGRAS)
     * @return array{leitura_id: ?int, localizacao_id: ?int, alertas: Alerta[]}
     */
    public function processar(Coleira $coleira, array $dados): array
    {
        return DB::transaction(function () use ($coleira, $dados) {
            $animal = $coleira->animal;
            $quando = $this->momento($dados['registrado_em'] ?? null);
            $novos = [];

            // 1) Sinais vitais
            $leitura = null;
            if (isset($dados['batimento_cardiaco']) || isset($dados['temperatura'])) {
                $leitura = LeituraSaude::create([
                    'animal_id'          => $animal->id,
                    'coleira_id'         => $coleira->id,
                    'batimento_cardiaco' => $dados['batimento_cardiaco'] ?? null,
                    'temperatura'        => $dados['temperatura'] ?? null,
                    'registrado_em'      => $quando,
                ]);
            }

            // 2) Posição GPS
            $localizacao = null;
            if (isset($dados['latitude'], $dados['longitude'])) {
                $localizacao = Localizacao::create([
                    'coleira_id'    => $coleira->id,
                    'latitude'      => $dados['latitude'],
                    'longitude'     => $dados['longitude'],
                    'precisao'      => $dados['precisao'] ?? null,
                    'registrado_em' => $quando,
                ]);

                // Envio em lote pode chegar fora de ordem: só avança a
                // "última posição" se este ponto for mais recente.
                if (! $coleira->localizacao_atualizada_em || $quando->gte($coleira->localizacao_atualizada_em)) {
                    $coleira->fill([
                        'latitude'                  => $dados['latitude'],
                        'longitude'                 => $dados['longitude'],
                        'precisao'                  => $dados['precisao'] ?? null,
                        'localizacao_atualizada_em' => $quando,
                    ]);
                }
            }

            // 3) Bateria
            if (isset($dados['bateria_percentual'])
                && (! $coleira->bateria_atualizada_em || $quando->gte($coleira->bateria_atualizada_em))) {
                $coleira->bateria_percentual = (int) $dados['bateria_percentual'];
                $coleira->bateria_status = $coleira->calcularBateriaStatus();
                $coleira->bateria_atualizada_em = $quando;
            }

            if (! empty($dados['firmware_versao'])) {
                $coleira->firmware_versao = $dados['firmware_versao'];
            }

            // Chegou dado: a coleira está viva.
            $estavaSemSinal = $coleira->status === 'sem_sinal';
            $coleira->ultimo_contato_em = now();
            if ($estavaSemSinal) {
                $coleira->status = 'ativa';
            }
            $coleira->save();

            if ($estavaSemSinal) {
                $this->alertas->resolver($animal, 'sem_sinal');
            }

            // 4) Alertas
            $novos = array_merge(
                $this->avaliarBatimento($animal, $coleira, $dados['batimento_cardiaco'] ?? null),
                $this->avaliarTemperatura($animal, $coleira, $dados['temperatura'] ?? null),
                $this->avaliarBateria($animal, $coleira, isset($dados['bateria_percentual'])),
                $this->avaliarZona($animal, $coleira, $dados['latitude'] ?? null, $dados['longitude'] ?? null),
            );

            $this->alertas->atualizarStatusDoAnimal($animal);

            return [
                'leitura_id'     => $leitura?->id,
                'localizacao_id' => $localizacao?->id,
                'alertas'        => $novos,
            ];
        });
    }

    /** Data da medição: a da coleira (se válida) ou agora. Nunca no futuro. */
    private function momento(?string $registradoEm): Carbon
    {
        $quando = $registradoEm ? Carbon::parse($registradoEm)->setTimezone(config('app.timezone')) : now();

        return $quando->gt(now()) ? now() : $quando;
    }

    private function avaliarBatimento(Animal $animal, Coleira $coleira, ?int $bpm): array
    {
        if ($bpm === null) {
            return [];
        }

        $ref = $animal->faixaReferencia();
        $nivel = Animal::nivelNaFaixa($bpm, $ref);

        if ($nivel === null) {
            return [];
        }
        if ($nivel === 'normal') {
            $this->alertas->resolver($animal, 'batimento');
            return [];
        }

        $sentido = $bpm > $ref['max'] ? 'acima' : 'abaixo';
        [$alerta, $novo] = $this->alertas->abrir(
            $animal, $coleira, 'batimento', $nivel,
            "Batimento {$sentido} do normal",
            "{$animal->nome} está com {$bpm} bpm (normal: {$ref['min']}–{$ref['max']} bpm).",
            "{$bpm} bpm",
        );

        return $novo ? [$alerta] : [];
    }

    private function avaliarTemperatura(Animal $animal, Coleira $coleira, ?float $temperatura): array
    {
        if ($temperatura === null) {
            return [];
        }

        $ref = $animal->faixaTemperatura();
        $nivel = Animal::nivelNaFaixa($temperatura, $ref);

        if ($nivel === null) {
            return [];
        }
        if ($nivel === 'normal') {
            $this->alertas->resolver($animal, 'temperatura');
            return [];
        }

        $fmt = fn (float $v) => number_format($v, 1, ',', '');
        $titulo = $temperatura > $ref['max'] ? 'Temperatura alta' : 'Temperatura baixa';
        [$alerta, $novo] = $this->alertas->abrir(
            $animal, $coleira, 'temperatura', $nivel, $titulo,
            "{$animal->nome} está com {$fmt($temperatura)} °C (normal: {$fmt($ref['min'])}–{$fmt($ref['max'])} °C).",
            "{$fmt($temperatura)} °C",
        );

        return $novo ? [$alerta] : [];
    }

    private function avaliarBateria(Animal $animal, Coleira $coleira, bool $enviouBateria): array
    {
        if (! $enviouBateria) {
            return [];
        }

        if ($coleira->bateria_status === 'normal') {
            $this->alertas->resolver($animal, 'bateria');
            return [];
        }

        $critica = $coleira->bateria_status === 'critica';
        [$alerta, $novo] = $this->alertas->abrir(
            $animal, $coleira, 'bateria', $critica ? 'emergencia' : 'alerta',
            $critica ? 'Bateria crítica' : 'Bateria baixa',
            "A coleira de {$animal->nome} está com {$coleira->bateria_percentual}% de bateria. Recarregue para não perder o monitoramento.",
            "{$coleira->bateria_percentual}%",
        );

        return $novo ? [$alerta] : [];
    }

    private function avaliarZona(Animal $animal, Coleira $coleira, ?float $lat, ?float $lng): array
    {
        if ($lat === null || $lng === null) {
            return [];
        }

        $zonas = $animal->zonasSeguras()->where('ativa', true)->get();
        if ($zonas->isEmpty()) {
            return [];
        }

        if ($zonas->contains(fn ($z) => $z->contem($lat, $lng))) {
            $this->alertas->resolver($animal, 'zona_segura');
            return [];
        }

        $maisProxima = $zonas->sortBy(fn ($z) => $z->distanciaAte($lat, $lng))->first();
        $distancia = (int) round($maisProxima->distanciaAte($lat, $lng));

        [$alerta, $novo] = $this->alertas->abrir(
            $animal, $coleira, 'zona_segura', 'alerta',
            'Fora da zona segura',
            "{$animal->nome} está a {$distancia} m do centro de \"{$maisProxima->nome}\" (limite: {$maisProxima->raio_metros} m).",
            "{$distancia} m",
        );

        return $novo ? [$alerta] : [];
    }
}
