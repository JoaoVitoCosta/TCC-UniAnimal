<?php

use App\Models\Animal;
use App\Models\Coleira;
use App\Models\LeituraSaude;
use App\Services\AlertaService;
use App\Services\TelemetriaService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Str;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Coleiras (IoT)
|--------------------------------------------------------------------------
*/

// Gera (ou troca) a chave do dispositivo. A chave aparece UMA vez: grave no firmware.
Artisan::command('coleira:chave {codigo}', function () {
    $coleira = Coleira::where('codigo', $this->argument('codigo'))->first();

    if (! $coleira) {
        $this->error('Coleira não encontrada.');
        return 1;
    }

    if ($coleira->chave_hash && ! $this->confirm('Esta coleira já tem chave. Gerar uma nova invalida a antiga. Continuar?', true)) {
        return 0;
    }

    $chave = $coleira->gerarChave();
    $this->info("Chave da coleira {$coleira->codigo} (copie agora, ela não será mostrada de novo):");
    $this->line($chave);
})->purpose('Gera a chave de autenticação do hardware para a API IoT');

// Cria coleiras novas em estoque, cada uma com código e chave.
Artisan::command('coleiras:provisionar {quantidade=5}', function () {
    $linhas = [];
    for ($i = 0; $i < (int) $this->argument('quantidade'); $i++) {
        do {
            $codigo = 'COL-' . strtoupper(Str::random(8));
        } while (Coleira::where('codigo', $codigo)->exists());

        $coleira = Coleira::create(['codigo' => $codigo, 'status' => 'disponivel']);
        $linhas[] = [$codigo, $coleira->gerarChave()];
    }

    $this->table(['Código (impresso na coleira)', 'Chave (gravar no firmware)'], $linhas);
    $this->warn('Guarde as chaves agora: no banco fica só o hash.');
})->purpose('Cria coleiras novas em estoque com código e chave');

// Marca como "sem sinal" quem parou de transmitir (agendado a cada minuto).
Artisan::command('coleiras:verificar-sinal', function (AlertaService $alertas) {
    $total = $alertas->verificarColeirasSemSinal();
    $this->info("{$total} coleira(s) passaram para \"sem sinal\".");
})->purpose('Abre alerta para coleiras que pararam de enviar telemetria');

Schedule::command('coleiras:verificar-sinal')->everyMinute()->withoutOverlapping();

/*
|--------------------------------------------------------------------------
| Simuladores (demonstração sem hardware)
|--------------------------------------------------------------------------
*/

// Simula a COLEIRA de verdade: envia telemetria por HTTP para a API IoT,
// exatamente como o firmware fará. Ex.:
//   php artisan iot:simular COL-DC9FEAAA
//   php artisan iot:simular COL-DC9FEAAA --cenario=febre
Artisan::command('iot:simular {codigo} {--chave=} {--cenario=normal} {--intervalo=10} {--vezes=0} {--url=http://127.0.0.1:8000}', function () {
    $coleira = Coleira::with('animal')->where('codigo', $this->argument('codigo'))->first();

    if (! $coleira || ! $coleira->animal) {
        $this->error('Coleira não encontrada ou não vinculada a um animal.');
        return 1;
    }

    $cenarios = ['normal', 'febre', 'taquicardia', 'bradicardia', 'fuga', 'bateria'];
    $cenario = $this->option('cenario');
    if (! in_array($cenario, $cenarios, true)) {
        $this->error('Cenário inválido. Use: ' . implode(', ', $cenarios));
        return 1;
    }

    $chave = $this->option('chave');
    if (! $chave) {
        $chave = $coleira->gerarChave();
        $this->warn("Sem --chave: gerada uma chave nova para o teste ({$chave}).");
    }

    $animal = $coleira->animal;
    $url = rtrim($this->option('url'), '/') . '/api/v1/iot/telemetria';
    $bpm = $animal->ultimoBatimento?->batimento_cardiaco;
    $temp = $animal->ultimaTemperatura?->temperatura;
    $lat = (float) ($coleira->latitude ?? -21.7050);
    $lng = (float) ($coleira->longitude ?? -47.2740);
    $bateria = $coleira->bateria_percentual ?: 100;
    $ref = $animal->faixaReferencia();
    $refT = $animal->faixaTemperatura();

    $this->info("Simulando {$coleira->codigo} ({$animal->nome}) · cenário {$cenario} · POST {$url}");
    $this->line('Ctrl+C para parar.');

    for ($n = 1; ! $this->option('vezes') || $n <= (int) $this->option('vezes'); $n++) {
        $bpm = LeituraSaude::simularBatimento($animal, $bpm);
        $temp = LeituraSaude::simularTemperatura($animal, $temp);
        $lat += random_int(-30, 30) / 100000;
        $lng += random_int(-30, 30) / 100000;
        $bateria = max(1, $bateria - random_int(0, 1));

        match ($cenario) {
            'febre'       => $temp = ($refT['emergencia_max'] ?? 40.0) + 0.3,
            'taquicardia' => $bpm = ($ref['emergencia_max'] ?? 200) + 15,
            'bradicardia' => $bpm = max(10, ($ref['emergencia_min'] ?? 50) - 10),
            'fuga'        => [$lat, $lng] = [$lat + 0.01, $lng + 0.01],
            'bateria'     => $bateria = 12,
            default       => null,
        };

        $corpo = [
            'batimento_cardiaco' => $bpm,
            'temperatura'        => $temp,
            'latitude'           => round($lat, 7),
            'longitude'          => round($lng, 7),
            'precisao'           => random_int(3, 12),
            'bateria_percentual' => $bateria,
            'firmware_versao'    => 'sim-1.0',
            'registrado_em'      => now()->toIso8601String(),
        ];

        try {
            $resp = Http::withToken($chave)->acceptJson()->timeout(10)->post($url, $corpo);
            $alertas = collect($resp->json('alertas_gerados', []))->pluck('titulo')->join(', ');
            $this->line(sprintf('%s  HTTP %d  %d bpm · %.1f °C · bateria %d%%  → status: %s%s',
                now()->format('H:i:s'), $resp->status(), $bpm, $temp, $bateria,
                $resp->json('status_animal', '?'), $alertas ? "  ⚠ novo alerta: {$alertas}" : ''));

            if ($resp->status() >= 400) {
                $this->error('Resposta: ' . $resp->body());
            }
        } catch (\Throwable $e) {
            $this->error('Falha ao conectar em ' . $url . ' — o "php artisan serve" está rodando?');
            return 1;
        }

        if ($this->option('vezes') && $n >= (int) $this->option('vezes')) {
            break;
        }
        sleep((int) $this->option('intervalo'));
    }

    return 0;
})->purpose('Simula a coleira enviando telemetria por HTTP para a API IoT');

// Atalho antigo: simula pelo ID do animal, sem HTTP (processa direto no servidor).
//   php artisan batimentos:simular 7
Artisan::command('batimentos:simular {animal=7} {--intervalo=10}', function (TelemetriaService $telemetria) {
    $animal = Animal::with('coleira')->find($this->argument('animal'));

    if (! $animal?->coleira) {
        $this->error('Animal não encontrado ou sem coleira.');
        return 1;
    }

    $bpm = $animal->ultimoBatimento?->batimento_cardiaco;
    $temp = $animal->ultimaTemperatura?->temperatura;
    $this->info("Enviando sinais vitais de {$animal->nome} a cada {$this->option('intervalo')}s (Ctrl+C para parar)...");

    while (true) {
        $bpm = LeituraSaude::simularBatimento($animal, $bpm);
        $temp = LeituraSaude::simularTemperatura($animal, $temp);
        $telemetria->processar($animal->coleira->fresh('animal'), ['batimento_cardiaco' => $bpm, 'temperatura' => $temp]);

        $this->line(now()->format('H:i:s') . " — {$bpm} bpm · {$temp} °C");
        sleep((int) $this->option('intervalo'));
    }
})->purpose('Insere sinais vitais simulados para um animal (sem HTTP)');
