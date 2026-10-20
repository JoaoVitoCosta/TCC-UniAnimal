<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Alerta gerado automaticamente a partir da telemetria da coleira
 * (ver App\Services\AlertaService). Fica "aberto" até a situação voltar ao
 * normal (resolvido_em) e "não lido" até o tutor abrir (lido_em).
 */
class Alerta extends Model
{
    protected $table = 'alertas';

    public const TIPOS = ['batimento', 'temperatura', 'bateria', 'sem_sinal', 'zona_segura'];

    protected $fillable = ['animal_id', 'coleira_id', 'tipo', 'nivel', 'titulo', 'mensagem', 'valor', 'lido_em', 'resolvido_em'];

    protected $casts = [
        'lido_em'      => 'datetime',
        'resolvido_em' => 'datetime',
    ];

    public function animal()
    {
        return $this->belongsTo(Animal::class);
    }

    public function coleira()
    {
        return $this->belongsTo(Coleira::class);
    }

    public function scopeAbertos($query)
    {
        return $query->whereNull('resolvido_em');
    }

    public function scopeNaoLidos($query)
    {
        return $query->whereNull('lido_em');
    }

    public function getAbertoAttribute(): bool
    {
        return $this->resolvido_em === null;
    }

    /** Cor usada nos badges do site (ok/warn/err). */
    public function getCorAttribute(): string
    {
        return $this->nivel === 'emergencia' ? 'err' : 'warn';
    }

    public function getNivelLabelAttribute(): string
    {
        return $this->nivel === 'emergencia' ? 'Emergência' : 'Alerta';
    }
}
