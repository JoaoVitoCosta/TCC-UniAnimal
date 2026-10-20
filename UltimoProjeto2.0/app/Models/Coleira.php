<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coleira extends Model
{
    use HasFactory;

    protected $table = 'coleiras';

    protected $fillable = [
        'animal_id',
        'codigo',
        'latitude',
        'longitude',
        'precisao',
        'localizacao_atualizada_em',
        'bateria_percentual',
        'bateria_status',
        'bateria_atualizada_em',
        'status',
        'ativada_em',
        'firmware_versao',
        'intervalo_envio_seg',
        'ultimo_contato_em',
    ];

    /** A chave do dispositivo nunca sai do servidor, nem em hash. */
    protected $hidden = ['chave_hash'];

    protected $casts = [
        'latitude'                  => 'decimal:7',
        'longitude'                 => 'decimal:7',
        'precisao'                  => 'decimal:2',
        'localizacao_atualizada_em' => 'datetime',
        'bateria_percentual'        => 'integer',
        'bateria_atualizada_em'     => 'datetime',
        'ativada_em'                => 'datetime',
        'intervalo_envio_seg'       => 'integer',
        'ultimo_contato_em'         => 'datetime',
    ];

    public function animal()
    {
        return $this->belongsTo(Animal::class);
    }

    public function apiTokens()
    {
        return $this->hasMany(ApiToken::class);
    }

    public function telemetrias()
    {
        return $this->hasMany(Telemetria::class)->orderByDesc('recebido_em');
    }

    /*
    |--------------------------------------------------------------------------
    | Chave do dispositivo (autenticação do hardware na API IoT)
    |--------------------------------------------------------------------------
    | O firmware envia "Authorization: Bearer <chave>". Guardamos só o SHA-256:
    | a chave em texto só existe no momento em que é gerada.
    */

    /** Gera (ou troca) a chave do dispositivo e devolve o texto puro UMA vez. */
    public function gerarChave(): string
    {
        $chave = 'uak_' . \Illuminate\Support\Str::random(40);
        $this->forceFill(['chave_hash' => hash('sha256', $chave)])->save();

        return $chave;
    }

    public static function porChave(string $chave): ?self
    {
        return $chave === '' ? null : static::where('chave_hash', hash('sha256', $chave))->first();
    }

    /** Considera sem sinal quem não envia há 3 intervalos (mínimo 10 min). */
    public function limiteSemSinal(): \Illuminate\Support\Carbon
    {
        return now()->subSeconds(max(600, 3 * ($this->intervalo_envio_seg ?: 60)));
    }

    /** Coleiras provisionadas mas ainda não vinculadas a nenhum animal. */
    public function scopeDisponiveis($query)
    {
        return $query->where('status', 'disponivel')->whereNull('animal_id');
    }

    /** Coleiras vinculadas e liberadas para uso (login/monitoramento). */
    public function scopeVinculaveis($query)
    {
        return $query->whereNotNull('animal_id')->where('status', '!=', 'inativa');
    }

    /**
     * Coleira com este código; se ainda não existir, cria em estoque
     * (trava de estoque desativada por enquanto).
     */
    public static function obterOuCriar(string $codigo): self
    {
        return static::firstOrCreate(
            ['codigo' => $codigo],
            ['status' => 'disponivel', 'bateria_percentual' => 100, 'bateria_status' => 'normal']
        );
    }

    public function estaDisponivel(): bool
    {
        return $this->status === 'disponivel' && $this->animal_id === null;
    }

    /**
     * Vincula esta coleira a um animal recém-cadastrado, já posicionada
     * na localização inicial (config services.coleira).
     */
    public function vincularAoAnimal(Animal $animal): void
    {
        $lat = config('services.coleira.latitude_inicial');
        $lng = config('services.coleira.longitude_inicial');

        $this->forceFill([
            'animal_id'                 => $animal->id,
            'status'                    => 'ativa',
            'ativada_em'                => now(),
            'latitude'                  => $lat,
            'longitude'                 => $lng,
            'localizacao_atualizada_em' => now(),
        ])->save();

        // Primeiro ponto da rota, para o mapa já mostrar o animal.
        $this->localizacoes()->create([
            'latitude'      => $lat,
            'longitude'     => $lng,
            'registrado_em' => now(),
        ]);
    }

    public function localizacoes()
    {
        return $this->hasMany(Localizacao::class)->orderByDesc('registrado_em');
    }

    public function ultimaLocalizacao()
    {
        return $this->hasOne(Localizacao::class)->latestOfMany('registrado_em');
    }

    /**
     * Recalcula o status da bateria a partir do percentual atual.
     */
    public function calcularBateriaStatus(): string
    {
        return match (true) {
            $this->bateria_percentual <= 15 => 'critica',
            $this->bateria_percentual <= 35 => 'baixa',
            default                          => 'normal',
        };
    }

    public function getBateriaStatusLabelAttribute(): string
    {
        return match ($this->bateria_status) {
            'critica' => 'Crítica',
            'baixa'   => 'Baixa',
            default   => 'Normal',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'inativa'   => 'Inativa',
            'sem_sinal' => 'Sem sinal',
            default     => 'Ativa',
        };
    }

    public function scopeComAlerta($query)
    {
        // Agrupamento explícito: sem os parênteses aqui, o orWhere() "escapa"
        // do whereIn('animal_id', ...) do chamador e pode vazar registros de
        // outros tutores. Sempre envolver condições OR dentro de where(fn...).
        return $query->where(function ($q) {
            $q->whereIn('bateria_status', ['baixa', 'critica'])
              ->orWhere('status', '!=', 'ativa');
        });
    }
}
