<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Leitura de sinais vitais enviada pela coleira (batimento e/ou temperatura).
 */
class LeituraSaude extends Model
{
    protected $table = 'leituras_saude';

    protected $fillable = ['animal_id', 'coleira_id', 'batimento_cardiaco', 'temperatura', 'registrado_em'];

    protected $casts = [
        'batimento_cardiaco' => 'integer',
        'temperatura'        => 'float',
        'registrado_em'      => 'datetime',
    ];

    public function animal()
    {
        return $this->belongsTo(Animal::class);
    }

    public function coleira()
    {
        return $this->belongsTo(Coleira::class);
    }

    /**
     * Próximo batimento SIMULADO (demonstração, sem coleira física): variação
     * suave em torno do anterior, dentro da faixa normal do animal
     * (Animal::REFERENCIA_BPM). Espécies sem faixa usam 60–160 bpm.
     */
    public static function simularBatimento(Animal $animal, ?int $anterior = null): int
    {
        $ref = $animal->faixaReferencia();
        [$min, $max] = $ref ? [$ref['min'], $ref['max']] : [60, 160];

        $base  = $anterior ?? intdiv($min + $max, 2);
        $passo = max(2, (int) round(($max - $min) * 0.08));

        return max($min, min($max, $base + random_int(-$passo, $passo)));
    }

    /** Próxima temperatura SIMULADA dentro da faixa normal do animal. */
    public static function simularTemperatura(Animal $animal, ?float $anterior = null): float
    {
        $ref = $animal->faixaTemperatura();
        [$min, $max] = $ref ? [$ref['min'], $ref['max']] : [37.5, 39.2];

        $base = $anterior ?? round(($min + $max) / 2, 1);

        return round(max($min, min($max, $base + random_int(-2, 2) / 10)), 1);
    }
}
