<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Token de acesso do aplicativo Flutter.
 *
 * O valor em texto puro só existe uma vez, no momento da criação (é isso
 * que é devolvido ao app). No banco, guardamos apenas o hash SHA-256 —
 * mesmo em caso de vazamento do banco de dados, os tokens em uso não
 * podem ser reconstruídos a partir do que está armazenado.
 */
class ApiToken extends Model
{
    protected $table = 'api_tokens';

    protected $fillable = [
        'tutor_id',
        'coleira_id',
        'token_hash',
        'nome_dispositivo',
        'last_used_at',
        'expires_at',
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
        'expires_at'   => 'datetime',
    ];

    public function tutor()
    {
        return $this->belongsTo(Tutor::class);
    }

    public function coleira()
    {
        return $this->belongsTo(Coleira::class);
    }

    /**
     * Gera um novo token para o tutor, persiste apenas o hash e retorna
     * o par [texto puro do token, registro ApiToken].
     */
    public static function issueFor(Tutor $tutor, ?Coleira $coleira = null, ?string $deviceName = null): array
    {
        $plainText = Str::random(64);

        $record = static::create([
            'tutor_id'         => $tutor->id,
            'coleira_id'       => $coleira?->id,
            'token_hash'       => hash('sha256', $plainText),
            'nome_dispositivo' => $deviceName,
            'expires_at'       => now()->addDays(90),
        ]);

        return [$plainText, $record];
    }

    public static function findValidByPlainText(string $plainText): ?self
    {
        $token = static::where('token_hash', hash('sha256', $plainText))->first();

        if (! $token) {
            return null;
        }

        if ($token->expires_at !== null && $token->expires_at->isPast()) {
            return null;
        }

        return $token;
    }

    public function markUsedNow(): void
    {
        $this->forceFill(['last_used_at' => now()])->save();
    }
}
