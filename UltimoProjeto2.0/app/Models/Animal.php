<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Animal extends Model
{
    use HasFactory;

    protected $table = 'animais';

    protected $fillable = [
        'tutor_id', 'nome', 'especie', 'raca',
        'porte', 'idade', 'peso', 'status',
    ];

    protected $casts = [
        'peso' => 'decimal:2',
    ];

    /**
     * "Orientação de batimentos" — frequência cardíaca em repouso (bpm) de
     * animais adultos, igual à tabela do app. Três níveis por animal:
     *   Normal      min..max
     *   Alerta      emergencia_min..(min-1)  e  (max+1)..emergencia_max
     *   Emergência  abaixo de emergencia_min ou acima de emergencia_max
     * Cães usam a linha do porte; as demais espécies, a da espécie.
     * Espécies fora da tabela (ex.: Bovino, Outro) não são classificadas.
     */
    public const REFERENCIA_BPM = [
        'cao-pequeno' => ['label' => 'Cão pequeno', 'min' => 80,  'max' => 140, 'emergencia_min' => 60,  'emergencia_max' => 170],
        'cao-medio'   => ['label' => 'Cão médio',   'min' => 70,  'max' => 120, 'emergencia_min' => 55,  'emergencia_max' => 150],
        'cao-grande'  => ['label' => 'Cão grande',  'min' => 60,  'max' => 100, 'emergencia_min' => 50,  'emergencia_max' => 130],
        'gato'        => ['label' => 'Gato',        'min' => 140, 'max' => 220, 'emergencia_min' => 120, 'emergencia_max' => 240],
        'coelho'      => ['label' => 'Coelho',      'min' => 130, 'max' => 325, 'emergencia_min' => 110, 'emergencia_max' => 350],
        'cavalo'      => ['label' => 'Cavalo',      'min' => 28,  'max' => 44,  'emergencia_min' => 24,  'emergencia_max' => 60],
    ];

    /**
     * Faixa de referência que vale para este animal (linha da tabela acima,
     * mais a "chave" usada para destacá-la na tela).
     *
     * @return array{chave: string, label: string, min: int, max: int, emergencia_min: int, emergencia_max: int}|null
     */
    public function faixaReferencia(): ?array
    {
        $especie = mb_strtolower(trim((string) $this->especie));

        $chave = in_array($especie, ['cachorro', 'cão', 'cao'], true)
            ? 'cao-' . $this->porte
            : $especie;

        $ref = self::REFERENCIA_BPM[$chave] ?? null;

        return $ref ? ['chave' => $chave] + $ref : null;
    }

    /**
     * Classifica um batimento: ['ok','Normal'], ['warn','Alerta'] ou
     * ['err','Emergência']. Null quando não há leitura ou faixa.
     */
    public function faixaBatimento(?int $bpm): ?array
    {
        $ref = $this->faixaReferencia();

        if ($bpm === null || ! $ref) {
            return null;
        }

        return match (true) {
            $bpm < $ref['emergencia_min'] || $bpm > $ref['emergencia_max'] => ['err', 'Emergência'],
            $bpm < $ref['min'] || $bpm > $ref['max']                       => ['warn', 'Alerta'],
            default                                                        => ['ok', 'Normal'],
        };
    }

    /**
     * Temperatura corporal de referência (°C) de animais adultos, mesmos três
     * níveis: Normal min..max · Alerta até emergencia_min/max · Emergência fora.
     * Valores aproximados, apenas para orientação.
     */
    public const REFERENCIA_TEMPERATURA = [
        'cao'    => ['label' => 'Cão',    'min' => 37.5, 'max' => 39.2, 'emergencia_min' => 36.5, 'emergencia_max' => 40.0],
        'gato'   => ['label' => 'Gato',   'min' => 38.0, 'max' => 39.2, 'emergencia_min' => 37.0, 'emergencia_max' => 40.0],
        'coelho' => ['label' => 'Coelho', 'min' => 38.5, 'max' => 40.0, 'emergencia_min' => 37.0, 'emergencia_max' => 41.0],
        'cavalo' => ['label' => 'Cavalo', 'min' => 37.2, 'max' => 38.3, 'emergencia_min' => 36.5, 'emergencia_max' => 39.5],
    ];

    /** Faixa de temperatura que vale para este animal (null se a espécie não tem). */
    public function faixaTemperatura(): ?array
    {
        $especie = mb_strtolower(trim((string) $this->especie));
        $chave = in_array($especie, ['cachorro', 'cão', 'cao'], true) ? 'cao' : $especie;
        $ref = self::REFERENCIA_TEMPERATURA[$chave] ?? null;

        return $ref ? ['chave' => $chave] + $ref : null;
    }

    /**
     * Nível de um valor numa faixa (de REFERENCIA_BPM ou REFERENCIA_TEMPERATURA):
     * 'normal', 'alerta' ou 'emergencia'. Null sem valor ou sem faixa.
     */
    public static function nivelNaFaixa(int|float|null $valor, ?array $ref): ?string
    {
        if ($valor === null || ! $ref) {
            return null;
        }

        return match (true) {
            $valor < $ref['emergencia_min'] || $valor > $ref['emergencia_max'] => 'emergencia',
            $valor < $ref['min'] || $valor > $ref['max']                       => 'alerta',
            default                                                            => 'normal',
        };
    }

    public function tutor()
    {
        return $this->belongsTo(Tutor::class);
    }

    public function coleira()
    {
        return $this->hasOne(Coleira::class);
    }

    /** Sinais vitais enviados pela coleira (tabela leituras_saude), mais recentes primeiro. */
    public function leituras()
    {
        return $this->hasMany(LeituraSaude::class)->orderByDesc('registrado_em');
    }

    /** Leituras que têm batimento (o gráfico usa só estas). */
    public function batimentos()
    {
        return $this->leituras()->whereNotNull('batimento_cardiaco');
    }

    public function ultimoBatimento()
    {
        return $this->hasOne(LeituraSaude::class)
            ->ofMany(['registrado_em' => 'max', 'id' => 'max'], fn ($q) => $q->whereNotNull('batimento_cardiaco'));
    }

    public function ultimaTemperatura()
    {
        return $this->hasOne(LeituraSaude::class)
            ->ofMany(['registrado_em' => 'max', 'id' => 'max'], fn ($q) => $q->whereNotNull('temperatura'));
    }

    public function alertas()
    {
        return $this->hasMany(Alerta::class)->latest();
    }

    public function alertasAbertos()
    {
        return $this->alertas()->abertos();
    }

    public function zonasSeguras()
    {
        return $this->hasMany(ZonaSegura::class);
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'saudavel'   => 'ok',
            'atencao'    => 'warn',
            'emergencia' => 'err',
            default      => 'ok',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'saudavel'   => 'Saudável',
            'atencao'    => 'Atenção',
            'emergencia' => 'Emergência',
            default      => 'Saudável',
        };
    }

    public function getEspecieEmojiAttribute(): string
    {
        return match (strtolower($this->especie)) {
            'cachorro', 'cão' => '🐕',
            'gato'            => '🐱',
            'coelho'          => '🐇',
            'cavalo'          => '🐴',
            'vaca', 'bovino'  => '🐄',
            default           => '🐾',
        };
    }

    public function getPorteLabelAttribute(): string
    {
        return match ($this->porte) {
            'pequeno' => 'Pequeno',
            'medio'   => 'Médio',
            'grande'  => 'Grande',
            default   => 'Médio',
        };
    }

    /**
     * Um animal está "monitorado" quando possui uma coleira ativa vinculada.
     */
    public function getMonitoradoAttribute(): bool
    {
        return $this->relationLoaded('coleira')
            ? (bool) $this->coleira && $this->coleira->status === 'ativa'
            : $this->coleira()->where('status', 'ativa')->exists();
    }

    public function scopePorStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeDoTutor($query, int $tutorId)
    {
        return $query->where('tutor_id', $tutorId);
    }
}
