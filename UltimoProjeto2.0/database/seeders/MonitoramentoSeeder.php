<?php

namespace Database\Seeders;

use App\Models\Alerta;
use App\Models\Coleira;
use App\Models\LeituraSaude;
use App\Services\TelemetriaService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;

/**
 * Prepara o monitoramento para demonstração, sem apagar nada:
 *
 *  - Sinais vitais: coleiras ativas com menos de 10 leituras recebem 12
 *    (batimento + temperatura, uma a cada 15 min, terminando agora), dentro
 *    da faixa normal da espécie/porte.
 *  - GPS: rota de pelo menos 10 pontos terminando na posição atual.
 *  - Zona segura "Casa" (raio 300 m) no início da rota, se o animal não tiver.
 *  - Chave do dispositivo para toda coleira sem chave. As chaves geradas são
 *    gravadas em storage/app/iot/chaves_coleiras.csv (único lugar em texto).
 *  - Status coerente: animal em "atenção"/"emergência" sem alerta aberto
 *    recebe uma telemetria que reproduz o problema, processada pelo
 *    TelemetriaService real (o alerta é gerado pelas regras de verdade).
 *  - Estoque: códigos COL-DEMO-0001..0010.
 *
 * Pode rodar quantas vezes quiser: php artisan db:seed --class=MonitoramentoSeeder
 */
class MonitoramentoSeeder extends Seeder
{
    private const LEITURAS = 12;
    private const PONTOS_ROTA = 10;
    private const INTERVALO_MIN = 15;

    /** Posição padrão (Tambaú/SP) para coleira ativa ainda sem GPS. */
    private const POSICAO_PADRAO = [-21.7050, -47.2740];

    public function run(TelemetriaService $telemetria): void
    {
        foreach (range(1, 10) as $n) {
            Coleira::firstOrCreate(
                ['codigo' => sprintf('COL-DEMO-%04d', $n)],
                ['status' => 'disponivel', 'bateria_percentual' => 100, 'bateria_status' => 'normal']
            );
        }

        $coleiras = Coleira::with('animal')->whereNotNull('animal_id')->whereIn('status', ['ativa', 'sem_sinal'])->get();

        foreach ($coleiras as $coleira) {
            $this->completarLeituras($coleira);
            $this->completarRota($coleira);
            $this->criarZonaCasa($coleira);

            $ultima = $coleira->animal->leituras()->value('registrado_em');
            $coleira->forceFill(['ultimo_contato_em' => $ultima ?? now()])->save();

            $this->reproduzirStatus($coleira->fresh('animal'), $telemetria);
        }

        $this->gerarChaves();

        $this->command?->info('Monitoramento pronto. Códigos em estoque: '
            . Coleira::disponiveis()->orderBy('codigo')->pluck('codigo')->join(', '));
    }

    private function completarLeituras(Coleira $coleira): void
    {
        $animal = $coleira->animal;

        if ($animal->batimentos()->count() >= 10) {
            // Leituras antigas (só batimento) ganham temperatura, para o
            // card de temperatura do site/app ter dado desde o início.
            $temp = null;
            $animal->leituras()->whereNull('temperatura')->orderBy('registrado_em')->get()
                ->each(function (LeituraSaude $l) use ($animal, &$temp) {
                    $temp = LeituraSaude::simularTemperatura($animal, $temp);
                    $l->update(['temperatura' => $temp]);
                });
            return;
        }

        $bpm = null;
        $temp = null;
        for ($i = self::LEITURAS - 1; $i >= 0; $i--) {
            $bpm = LeituraSaude::simularBatimento($animal, $bpm);
            $temp = LeituraSaude::simularTemperatura($animal, $temp);
            $animal->leituras()->create([
                'coleira_id'         => $coleira->id,
                'batimento_cardiaco' => $bpm,
                'temperatura'        => $temp,
                'registrado_em'      => now()->subMinutes($i * self::INTERVALO_MIN),
            ]);
        }
    }

    private function completarRota(Coleira $coleira): void
    {
        $existentes = $coleira->localizacoes()->count();
        if ($existentes >= self::PONTOS_ROTA) {
            return;
        }

        // Caminha para trás a partir da posição atual (passos de ~30–120 m),
        // assim o último ponto da rota coincide com a posição da coleira.
        [$lat, $lng] = $coleira->latitude !== null
            ? [(float) $coleira->latitude, (float) $coleira->longitude]
            : self::POSICAO_PADRAO;

        $maisAntiga = $coleira->localizacoes()->min('registrado_em');
        $fim = $maisAntiga ? Carbon::parse($maisAntiga) : now()->addMinutes(self::INTERVALO_MIN);

        for ($i = 1; $i <= self::PONTOS_ROTA - $existentes; $i++) {
            $lat += random_int(-100, 100) / 100000;
            $lng += random_int(-100, 100) / 100000;
            $coleira->localizacoes()->create([
                'latitude'      => $lat,
                'longitude'     => $lng,
                'precisao'      => random_int(3, 12),
                'registrado_em' => $fim->copy()->subMinutes($i * self::INTERVALO_MIN),
            ]);
        }

        if ($coleira->latitude === null) {
            $ultima = $coleira->localizacoes()->first();
            $coleira->update([
                'latitude'                  => $ultima->latitude,
                'longitude'                 => $ultima->longitude,
                'precisao'                  => $ultima->precisao,
                'localizacao_atualizada_em' => $ultima->registrado_em,
            ]);
        }
    }

    /** Zona "Casa" centrada na posição atual, cobrindo a rota recente. */
    private function criarZonaCasa(Coleira $coleira): void
    {
        if ($coleira->animal->zonasSeguras()->exists() || $coleira->latitude === null) {
            return;
        }

        $centro = [(float) $coleira->latitude, (float) $coleira->longitude];
        $zona = $coleira->animal->zonasSeguras()->make([
            'nome' => 'Casa', 'latitude' => $centro[0], 'longitude' => $centro[1], 'raio_metros' => 300,
        ]);

        // Raio suficiente para conter os pontos da rota de demonstração.
        $maior = $coleira->localizacoes()->get()
            ->max(fn ($l) => $zona->distanciaAte((float) $l->latitude, (float) $l->longitude)) ?? 0;
        $zona->raio_metros = max(300, (int) ceil(($maior + 50) / 50) * 50);
        $zona->save();
    }

    /**
     * Animal marcado como atenção/emergência sem alerta aberto: envia uma
     * telemetria que reproduz o problema pelo serviço real de telemetria.
     */
    private function reproduzirStatus(Coleira $coleira, TelemetriaService $telemetria): void
    {
        $animal = $coleira->animal;
        if ($animal->status === 'saudavel' || Alerta::where('animal_id', $animal->id)->abertos()->exists()) {
            return;
        }

        $ref = $animal->faixaReferencia();
        $dados = $animal->status === 'emergencia' && $ref
            ? ['batimento_cardiaco' => $ref['emergencia_max'] + 12, 'temperatura' => LeituraSaude::simularTemperatura($animal)]
            : ['bateria_percentual' => 28];

        $telemetria->processar($coleira, $dados + ['registrado_em' => now()->toIso8601String()]);
    }

    private function gerarChaves(): void
    {
        $novas = Coleira::whereNull('chave_hash')->orderBy('codigo')->get()
            ->map(fn (Coleira $c) => [$c->codigo, $c->gerarChave(), $c->animal?->nome ?? '(estoque)']);

        if ($novas->isEmpty()) {
            return;
        }

        $arquivo = storage_path('app/iot/chaves_coleiras.csv');
        File::ensureDirectoryExists(dirname($arquivo));
        $novoArquivo = ! File::exists($arquivo);
        $csv = fopen($arquivo, 'a');
        if ($novoArquivo) {
            fputcsv($csv, ['codigo', 'chave_dispositivo', 'animal']);
        }
        $novas->each(fn ($linha) => fputcsv($csv, $linha));
        fclose($csv);

        $this->command?->table(['Código', 'Chave do dispositivo', 'Animal'], $novas->all());
        $this->command?->warn("Chaves salvas em {$arquivo} — guarde e apague o arquivo depois de gravar no firmware.");
    }
}
