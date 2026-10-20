<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Cerca virtual circular do animal. Se a coleira reportar uma posição fora
 * de todas as zonas ativas, é aberto um alerta "zona_segura".
 */
class ZonaSegura extends Model
{
    protected $table = 'zonas_seguras';

    protected $fillable = ['animal_id', 'nome', 'latitude', 'longitude', 'raio_metros', 'ativa'];

    protected $casts = [
        'latitude'    => 'float',
        'longitude'   => 'float',
        'raio_metros' => 'integer',
        'ativa'       => 'boolean',
    ];

    public function animal()
    {
        return $this->belongsTo(Animal::class);
    }

    /** Distância em metros entre o centro da zona e um ponto (fórmula de Haversine). */
    public function distanciaAte(float $latitude, float $longitude): float
    {
        $r = 6371000;
        $dLat = deg2rad($latitude - $this->latitude);
        $dLng = deg2rad($longitude - $this->longitude);
        $a = sin($dLat / 2) ** 2
           + cos(deg2rad($this->latitude)) * cos(deg2rad($latitude)) * sin($dLng / 2) ** 2;

        return $r * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    public function contem(float $latitude, float $longitude): bool
    {
        return $this->distanciaAte($latitude, $longitude) <= $this->raio_metros;
    }
}
