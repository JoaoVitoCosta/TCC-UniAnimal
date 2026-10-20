<?php

namespace Database\Seeders;

use App\Models\Animal;
use App\Models\Historico;
use Illuminate\Database\Seeder;

/**
 * Leituras de batimento cardíaco de demonstração para o pet id 7 (Belinha),
 * para visualizar no app Flutter.
 *
 * php artisan db:seed --class=HistoricoSeeder
 */
class HistoricoSeeder extends Seeder
{
    private const ANIMAL_ID = 7;

    public function run(): void
    {
        $animal = Animal::find(self::ANIMAL_ID);

        if (! $animal) {
            $this->command?->warn('Animal id ' . self::ANIMAL_ID . ' não encontrado — seed ignorado.');
            return;
        }

        // Recria só as leituras deste pet, para o seed poder rodar de novo.
        Historico::where('animal_id', $animal->id)->delete();

        // Uma leitura a cada 15 minutos, da mais antiga para a mais recente.
        $leituras = [
            [88,  '-23.5034, -46.6601'],
            [92,  '-23.5041, -46.6587'],
            [85,  '-23.5052, -46.6573'],
            [110, '-23.5067, -46.6522'],
            [124, '-23.5101, -46.6489'],
            [118, '-23.5143, -46.6461'],
            [97,  '-23.5209, -46.6456'],
            [90,  '-23.5067, -46.6522'],
            [84,  '-23.4943, -46.6513'],
            [95,  '-23.5098, -46.6744'],
        ];

        $inicio = now()->subMinutes(15 * (count($leituras) - 1));

        foreach ($leituras as $i => [$bpm, $localizacao]) {
            Historico::create([
                'animal_id'          => $animal->id,
                'nome'               => $animal->nome,
                'localizacao'        => $localizacao,
                'batimento_cardiaco' => $bpm,
                'registrado_em'      => $inicio->copy()->addMinutes(15 * $i),
            ]);
        }
    }
}
