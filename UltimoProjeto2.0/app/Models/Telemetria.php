<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Registro bruto de cada envio recebido na API IoT — o que a coleira mandou,
 * de onde e se foi aceito. Serve para depurar o firmware.
 */
class Telemetria extends Model
{
    protected $table = 'telemetrias';

    public $timestamps = false;

    protected $fillable = ['coleira_id', 'payload', 'ip', 'situacao', 'erro', 'recebido_em'];

    protected $casts = [
        'payload'     => 'array',
        'recebido_em' => 'datetime',
    ];

    public function coleira()
    {
        return $this->belongsTo(Coleira::class);
    }
}
